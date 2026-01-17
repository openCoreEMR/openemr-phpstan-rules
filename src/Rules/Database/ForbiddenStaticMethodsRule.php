<?php

/**
 * Custom PHPStan Rule to Block Certain Static Method Calls
 *
 * This rule prevents use of specific static methods that should be avoided
 * in favor of safer alternatives.
 *
 * @package   OpenCoreEMR
 * @link      https://opencoreemr.com
 * @author    Michael A. Smith <michael@opencoreemr.com>
 * @copyright Copyright (c) 2026 OpenCoreEMR Inc
 * @license   https://github.com/openCoreEMR/openemr-phpstan-rules/blob/main/LICENSE GNU General Public License 3
 */

namespace OpenCoreEMR\PHPStan\Rules\Database;

use PhpParser\Node;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * @implements Rule<StaticCall>
 */
class ForbiddenStaticMethodsRule implements Rule
{
    /**
     * Map of forbidden classes and static methods to their error messages
     */
    private const FORBIDDEN_METHODS = [
        'OpenEMR\\Common\\Database\\QueryUtils' => [
            'startTransaction' => 'Use QueryUtils::inTransaction() wrapper instead.',
            'commitTransaction' => 'Use QueryUtils::inTransaction() wrapper instead.',
            'rollbackTransaction' => 'Use QueryUtils::inTransaction() wrapper instead.',
        ],
    ];

    public function getNodeType(): string
    {
        return StaticCall::class;
    }

    /**
     * @param StaticCall $node
     * @return array<\PHPStan\Rules\RuleError>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        // Method name must be an Identifier (not a dynamic call)
        if (!($node->name instanceof Identifier)) {
            return [];
        }

        // Class must be a Name (not a dynamic class)
        if (!($node->class instanceof Name)) {
            return [];
        }

        $className = $node->class->toString();
        $methodName = $node->name->toString();

        // Check if the class has any forbidden methods
        if (!array_key_exists($className, self::FORBIDDEN_METHODS)) {
            return [];
        }

        // If it does, check if the actual call is one of them
        $forbiddenClassMethods = self::FORBIDDEN_METHODS[$className];
        if (!array_key_exists($methodName, $forbiddenClassMethods)) {
            return [];
        }

        $message = sprintf(
            '%s::%s() is deprecated. %s',
            $className,
            $methodName,
            $forbiddenClassMethods[$methodName],
        );

        return [
            RuleErrorBuilder::message($message)
                ->identifier('openemr.deprecatedStaticMethod')
                ->build()
        ];
    }
}
