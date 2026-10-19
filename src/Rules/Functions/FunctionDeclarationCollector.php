<?php

/**
 * Collects every function declaration for DuplicateGlobalFunctionRule
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
use PhpParser\Node\Stmt\Function_;
use PHPStan\Analyser\Scope;
use PHPStan\Collectors\Collector;

/**
 * Records the fully qualified name and start line of each named function.
 *
 * Methods and closures are different node types, so only declarations that
 * land in PHP's global function table are collected.
 *
 * @implements Collector<Function_, array{string, int}>
 */
final class FunctionDeclarationCollector implements Collector
{
    public function getNodeType(): string
    {
        return Function_::class;
    }

    /**
     * @param Function_ $node
     * @return array{string, int}
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $name = $node->namespacedName ?? $node->name;

        return [$name->toString(), $node->getStartLine()];
    }
}
