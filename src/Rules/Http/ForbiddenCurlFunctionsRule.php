<?php

/**
 * Custom PHPStan Rule to Forbid Raw curl_* Functions
 *
 * This rule prevents use of raw curl_* functions as the project is migrating
 * to use PSR-18 HTTP clients for better testability, error handling, and PSR-7 compliance.
 *
 * @package   OpenCoreEMR
 * @link      https://opencoreemr.com
 * @author    Michael A. Smith <michael@opencoreemr.com>
 * @copyright Copyright (c) 2026 OpenCoreEMR Inc
 * @license   https://github.com/openCoreEMR/openemr-phpstan-rules/blob/main/LICENSE GNU General Public License 3
 */

namespace OpenCoreEMR\PHPStan\Rules\Http;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * @implements Rule<FuncCall>
 */
class ForbiddenCurlFunctionsRule implements Rule
{
    /**
     * Pattern to match all curl_* functions
     */
    private const CURL_FUNCTION_PATTERN = '/^curl_/i';

    public function getNodeType(): string
    {
        return FuncCall::class;
    }

    /**
     * @param FuncCall $node
     * @return array<\PHPStan\Rules\RuleError>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (!($node->name instanceof Name)) {
            return [];
        }

        $functionName = $node->name->toString();

        // Check if it's a curl_* function
        if (!preg_match(self::CURL_FUNCTION_PATTERN, $functionName)) {
            return [];
        }

        return [
            RuleErrorBuilder::message(
                sprintf(
                    'Raw curl function %s() is forbidden. Use a PSR-18 HTTP client instead.',
                    $functionName
                )
            )
                ->identifier('openemr.forbiddenCurlFunction')
                ->tip('See https://www.php-fig.org/psr/psr-18/ for PSR-18 HTTP Client information')
                ->build()
        ];
    }
}
