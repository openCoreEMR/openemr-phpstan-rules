<?php

/**
 * Custom PHPStan Rule to Forbid Legacy Response Methods in Controllers
 *
 * This rule prevents use of header(), http_response_code(), die(), exit,
 * and direct echo in controller classes. Use Symfony Response objects instead.
 *
 * @package   OpenCoreEMR
 * @author    Michael A. Smith <michael@opencoreemr.com>
 * @copyright Copyright (c) 2025 OpenCoreEMR Inc
 * @license   https://github.com/OpenCoreEMR/openemr-phpstan-rules/blob/master/LICENSE GNU General Public License 3
 */

namespace OpenCoreEMR\PHPStan\Rules\Module;

use PhpParser\Node;
use PhpParser\Node\Expr\Exit_;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Echo_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * @implements Rule<Node>
 */
class NoLegacyResponseMethodsRule implements Rule
{
    private const FORBIDDEN_FUNCTIONS = [
        'header' => 'Use RedirectResponse, Response, or JsonResponse',
        'http_response_code' => 'Use Response constructor with status code',
        'die' => 'Throw an exception instead',
        'exit' => 'Throw an exception instead',
    ];

    public function getNodeType(): string
    {
        return Node::class;
    }

    /**
     * @param Node $node
     * @return array<\PHPStan\Rules\RuleError>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        // Only check inside classes with "Controller" in the name
        if (!$scope->isInClass()) {
            return [];
        }

        $className = $scope->getClassReflection()->getName();
        if (!str_contains($className, 'Controller')) {
            return [];
        }

        // Check for forbidden function calls
        if ($node instanceof FuncCall && $node->name instanceof Name) {
            $functionName = $node->name->toString();

            if (isset(self::FORBIDDEN_FUNCTIONS[$functionName])) {
                $replacement = self::FORBIDDEN_FUNCTIONS[$functionName];
                $message = sprintf(
                    'Function %s() is forbidden in controllers. %s.',
                    $functionName,
                    $replacement
                );

                return [
                    RuleErrorBuilder::message($message)
                        ->identifier('openemr.noLegacyResponseMethods')
                        ->tip('Controllers should return Response objects')
                        ->build()
                ];
            }
        }

        // Check for die/exit language constructs
        if ($node instanceof Exit_) {
            return [
                RuleErrorBuilder::message(
                    'die/exit is forbidden in controllers. Throw an exception instead.'
                )
                    ->identifier('openemr.noLegacyResponseMethods')
                    ->tip('Controllers should return Response objects')
                    ->build()
            ];
        }

        // Check for echo statements
        if ($node instanceof Echo_) {
            return [
                RuleErrorBuilder::message(
                    'Direct echo is forbidden in controllers. Use Response objects or Twig templates.'
                )
                    ->identifier('openemr.noLegacyResponseMethods')
                    ->tip('Return new Response($content) or use JsonResponse')
                    ->build()
            ];
        }

        return [];
    }
}
