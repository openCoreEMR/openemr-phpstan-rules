<?php

/**
 * Custom PHPStan Rule to Forbid Declaring One Function in Several Files
 *
 * PHP has one function table per request. When two files declare the same
 * function and one request includes both, PHP stops with the fatal error
 * "Cannot redeclare function". The collision only shows up on the code path
 * that loads both files, so it tends to surface in production.
 *
 * @package   OpenCoreEMR
 * @link      https://opencoreemr.com
 * @author    Michael A. Smith <michael@opencoreemr.com>
 * @copyright Copyright (c) 2026 OpenCoreEMR Inc
 * @license   https://github.com/openCoreEMR/openemr-phpstan-rules/blob/main/LICENSE GNU General Public License 3
 */

declare(strict_types=1);

namespace OpenCoreEMR\PHPStan\Rules\Functions;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\File\RelativePathHelper;
use PHPStan\Node\CollectedDataNode;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * Reports each unguarded declaration of a function name that is declared in
 * more than one file. Names compare case-insensitively, as PHP compares them.
 *
 * A declaration inside `if (!function_exists('name'))` cannot itself fatal,
 * so it is not reported. It still counts as a copy: an unguarded declaration
 * elsewhere fatals whenever the guarded one loads first.
 *
 * @implements Rule<CollectedDataNode>
 */
final readonly class DuplicateGlobalFunctionRule implements Rule
{
    public function __construct(
        private RelativePathHelper $relativePathHelper,
    ) {
    }

    public function getNodeType(): string
    {
        return CollectedDataNode::class;
    }

    /**
     * @param CollectedDataNode $node
     * @return list<IdentifierRuleError>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $guarded = [];
        foreach ($node->get(FunctionExistsGuardCollector::class) as $file => $lineLists) {
            foreach ($lineLists as $lines) {
                foreach ($lines as $line) {
                    $guarded[$file][$line] = true;
                }
            }
        }

        $byName = [];
        foreach ($node->get(FunctionDeclarationCollector::class) as $file => $declarations) {
            foreach ($declarations as [$name, $line]) {
                $byName[strtolower($name)][] = [
                    'file' => $file,
                    'name' => $name,
                    'line' => $line,
                    'guarded' => isset($guarded[$file][$line]),
                ];
            }
        }

        $errors = [];
        foreach ($byName as $declarations) {
            if (count(array_unique(array_column($declarations, 'file'))) < 2) {
                continue;
            }
            foreach ($declarations as $index => $declaration) {
                if ($declaration['guarded']) {
                    continue;
                }
                $errors[] = $this->buildError($declaration, $this->otherLocations($declarations, $index));
            }
        }

        return $errors;
    }

    /**
     * @param array{file: string, name: string, line: int, guarded: bool} $declaration
     */
    private function buildError(array $declaration, string $otherLocations): IdentifierRuleError
    {
        return RuleErrorBuilder::message(
            sprintf(
                'Function %s() is declared in more than one file. A request that loads two of them fails with "Cannot redeclare".',
                $declaration['name']
            )
        )
            ->identifier('openemr.duplicateGlobalFunction')
            ->file($declaration['file'])
            ->line($declaration['line'])
            ->tip(sprintf(
                'Also declared at %s. Keep one declaration in a shared file, or rename the copy.',
                $otherLocations
            ))
            ->build();
    }

    /**
     * @param list<array{file: string, name: string, line: int, guarded: bool}> $declarations
     */
    private function otherLocations(array $declarations, int $self): string
    {
        $locations = [];
        foreach ($declarations as $index => $other) {
            if ($index === $self) {
                continue;
            }
            $locations[] = sprintf(
                '%s:%d%s',
                $this->relativePathHelper->getRelativePath($other['file']),
                $other['line'],
                $other['guarded'] ? ' (guarded)' : ''
            );
        }

        sort($locations);

        return implode(', ', $locations);
    }
}
