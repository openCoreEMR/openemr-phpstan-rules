<?php

/**
 * Custom PHPStan Rule to Forbid Deprecated Database Methods
 *
 * This rule prevents use of deprecated database-related methods.
 * Contributors should use QueryUtils or DatabaseQueryTrait instead.
 *
 * @package   OpenCoreEMR
 * @link      https://opencoreemr.com
 * @author    Michael A. Smith <michael@opencoreemr.com>
 * @copyright Copyright (c) 2026 OpenCoreEMR Inc
 * @license   https://github.com/openCoreEMR/openemr-phpstan-rules/blob/main/LICENSE GNU General Public License 3
 */

namespace OpenCoreEMR\PHPStan\Rules\Database;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * @implements Rule<MethodCall>
 */
class ForbiddenMethodsRule implements Rule
{
    /**
     * Map of forbidden methods to their error messages
     *
     * (Ideally, these would be scoped to a specific class/interface, but most are
     * targeting globals lacking sufficient type info)
     */
    private const FORBIDDEN_METHODS = [
        'GenID' => 'Use QueryUtils::generateId() or QueryUtils::ediGenerateId() instead.',
    ];

    public function getNodeType(): string
    {
        return MethodCall::class;
    }

    /**
     * @param MethodCall $node
     * @return array<\PHPStan\Rules\RuleError>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (!($node->name instanceof Identifier)) {
            return [];
        }

        $methodName = $node->name->toString();

        // Only check if it's a forbidden method
        if (!isset(self::FORBIDDEN_METHODS[$methodName])) {
            return [];
        }

        $message = sprintf(
            '%s() is deprecated. %s',
            $methodName,
            self::FORBIDDEN_METHODS[$methodName],
        );

        return [
            RuleErrorBuilder::message($message)
                ->identifier('openemr.deprecatedMethod')
                ->tip('Or use DatabaseQueryTrait in your class')
                ->build()
        ];
    }
}
