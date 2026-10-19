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
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\BinaryOp\BooleanAnd;
use PhpParser\Node\Expr\BinaryOp\LogicalAnd;
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
 * The condition may AND the check with others, as in
 * `if (!function_exists('bar') && !function_exists('foo'))`, because the body
 * still only runs when `foo` is undeclared. Only a direct child of the `if`
 * body counts, and only when a literal names the function being declared.
 * Anything looser is not a reliable guard.
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
        $guardedNames = $this->guardedNames($node->cond);
        if ($guardedNames === []) {
            return null;
        }

        $lines = [];
        foreach ($node->stmts as $stmt) {
            if (!$stmt instanceof Function_) {
                continue;
            }
            $declared = $stmt->namespacedName ?? $stmt->name;
            if (in_array(strtolower($declared->toString()), $guardedNames, true)) {
                $lines[] = $stmt->getStartLine();
            }
        }

        return $lines === [] ? null : $lines;
    }

    /**
     * Lowercased names from every `!function_exists('name')` that the
     * condition ANDs together.
     *
     * @return list<string>
     */
    private function guardedNames(Expr $cond): array
    {
        if ($cond instanceof BooleanAnd || $cond instanceof LogicalAnd) {
            return [...$this->guardedNames($cond->left), ...$this->guardedNames($cond->right)];
        }
        if (!$cond instanceof BooleanNot) {
            return [];
        }
        $call = $cond->expr;
        if (!$call instanceof FuncCall || !$call->name instanceof Name) {
            return [];
        }
        if ($call->name->toLowerString() !== 'function_exists') {
            return [];
        }
        $args = $call->getArgs();
        if (!isset($args[0]) || !$args[0]->value instanceof String_) {
            return [];
        }

        return [strtolower(ltrim($args[0]->value->value, '\\'))];
    }
}
