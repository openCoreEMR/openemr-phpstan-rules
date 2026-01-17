<?php

/**
 * Custom PHPStan Rule to Require Catching \Throwable Instead of \Exception
 *
 * This rule enforces catching \Throwable to ensure both exceptions and errors
 * (like TypeError, ParseError) are caught, not just \Exception.
 *
 * @package   OpenCoreEMR
 * @author    Michael A. Smith <michael@opencoreemr.com>
 * @copyright Copyright (c) 2025 OpenCoreEMR Inc
 * @license   https://github.com/openCoreEMR/openemr-phpstan-rules/blob/main/LICENSE GNU General Public License 3
 */

namespace OpenCoreEMR\PHPStan\Rules\ExceptionHandling;

use PhpParser\Node;
use PhpParser\Node\Stmt\Catch_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * @implements Rule<Catch_>
 */
class CatchThrowableNotExceptionRule implements Rule
{
    public function getNodeType(): string
    {
        return Catch_::class;
    }

    /**
     * @param Catch_ $node
     * @return array<\PHPStan\Rules\RuleError>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $errors = [];

        foreach ($node->types as $type) {
            $typeName = $type->toString();

            // Check if catching \Exception (but not subclasses)
            if ($typeName === 'Exception' || $typeName === '\Exception') {
                $errors[] = RuleErrorBuilder::message(
                    'Catch \Throwable instead of \Exception to handle both exceptions and errors (TypeError, ParseError, etc.).'
                )
                    ->identifier('openemr.catchThrowable')
                    ->tip('Change catch (\Exception $e) to catch (\Throwable $e)')
                    ->build();
            }
        }

        return $errors;
    }
}
