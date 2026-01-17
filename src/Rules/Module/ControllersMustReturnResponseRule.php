<?php

/**
 * Custom PHPStan Rule to Require Controllers Return Response Objects
 *
 * This rule ensures controller methods return Symfony Response objects
 * instead of void or other types.
 *
 * @package   OpenCoreEMR
 * @author    Michael A. Smith <michael@opencoreemr.com>
 * @copyright Copyright (c) 2025 OpenCoreEMR Inc
 * @license   https://github.com/openCoreEMR/openemr-phpstan-rules/blob/main/LICENSE GNU General Public License 3
 */

namespace OpenCoreEMR\PHPStan\Rules\Module;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * @implements Rule<ClassMethod>
 */
class ControllersMustReturnResponseRule implements Rule
{
    public function getNodeType(): string
    {
        return ClassMethod::class;
    }

    /**
     * @param ClassMethod $node
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

        // Skip magic methods and constructors
        $methodName = $node->name->toString();
        if (str_starts_with($methodName, '__')) {
            return [];
        }

        // Skip private methods (they're internal helpers)
        if ($node->isPrivate()) {
            return [];
        }

        // Check if method has a return type
        if ($node->returnType === null) {
            return [
                RuleErrorBuilder::message(
                    sprintf(
                        'Controller method %s() must declare a return type (Response or subclass).',
                        $methodName
                    )
                )
                    ->identifier('openemr.controllerMustReturnResponse')
                    ->tip('Add return type: Response, JsonResponse, RedirectResponse, or BinaryFileResponse')
                    ->build()
            ];
        }

        // Check if return type is void
        $function = $scope->getFunction();
        if ($function instanceof \PHPStan\Reflection\Php\PhpFunctionFromParserNodeReflection) {
            $variants = $function->getVariants();
            if (count($variants) > 0) {
                $returnType = $variants[0]->getReturnType();
                if ($returnType->isVoid()->yes()) {
                    return [
                        RuleErrorBuilder::message(
                            sprintf(
                                'Controller method %s() must return Response object, not void.',
                                $methodName
                            )
                        )
                            ->identifier('openemr.controllerMustReturnResponse')
                            ->tip('Controllers should return Response, JsonResponse, RedirectResponse, or BinaryFileResponse')
                            ->build()
                    ];
                }
            }
        }

        return [];
    }
}
