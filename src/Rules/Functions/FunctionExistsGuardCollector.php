<?php

/**
 * Collects function declarations wrapped in a function_exists() guard
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
use PhpParser\Node\Expr\BooleanNot;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Function_;
use PhpParser\Node\Stmt\If_;
use PHPStan\Analyser\Scope;
use PHPStan\Collectors\Collector;

/**
 * Records the start lines of declarations shaped like
 * `if (!function_exists('foo')) { function foo() {} }`.
 *
 * Only a direct child of the `if` body counts, and only when the literal names
 * the function being declared. Anything looser is not a reliable guard.
 *
 * @implements Collector<If_, list<int>>
 */
final class FunctionExistsGuardCollector implements Collector
{
    public function getNodeType(): string
    {
        return If_::class;
    }

    /**
     * @param If_ $node
     * @return list<int>|null
     */
    public function processNode(Node $node, Scope $scope): ?array
    {
        $guardedName = $this->guardedName($node);
        if ($guardedName === null) {
            return null;
        }

        $lines = [];
        foreach ($node->stmts as $stmt) {
            if (!$stmt instanceof Function_) {
                continue;
            }
            $declared = $stmt->namespacedName ?? $stmt->name;
            if (strtolower($declared->toString()) === $guardedName) {
                $lines[] = $stmt->getStartLine();
            }
        }

        return $lines === [] ? null : $lines;
    }

    /**
     * Lowercased function name from a `!function_exists('name')` condition.
     */
    private function guardedName(If_ $node): ?string
    {
        $cond = $node->cond;
        if (!$cond instanceof BooleanNot) {
            return null;
        }
        $call = $cond->expr;
        if (!$call instanceof FuncCall || !$call->name instanceof Name) {
            return null;
        }
        if ($call->name->toLowerString() !== 'function_exists') {
            return null;
        }
        $args = $call->getArgs();
        if (!isset($args[0]) || !$args[0]->value instanceof String_) {
            return null;
        }

        return strtolower(ltrim($args[0]->value->value, '\\'));
    }
}
