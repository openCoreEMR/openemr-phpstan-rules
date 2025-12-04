<?php

/**
 * Custom PHPStan Rule to Forbid Superglobals in Controller Classes
 *
 * This rule prevents direct access to $_GET, $_POST, $_FILES, $_SERVER
 * in controller classes. Use Symfony Request object instead.
 *
 * @package   OpenCoreEMR
 * @author    Michael A. Smith <michael@opencoreemr.com>
 * @copyright Copyright (c) 2025 OpenCoreEMR Inc
 * @license   https://github.com/OpenCoreEMR/openemr-phpstan-rules/blob/master/LICENSE GNU General Public License 3
 */

namespace OpenCoreEMR\PHPStan\Rules\Module;

use PhpParser\Node;
use PhpParser\Node\Expr\Variable;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * @implements Rule<Variable>
 */
class NoSuperGlobalsInControllersRule implements Rule
{
    private const FORBIDDEN_SUPERGLOBALS = [
        '_GET' => '$request->query->get()',
        '_POST' => '$request->request->get()',
        '_FILES' => '$request->files->get()',
        '_SERVER' => '$request->server->get()',
    ];

    public function getNodeType(): string
    {
        return Variable::class;
    }

    /**
     * @param Variable $node
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

        // Check if this is a forbidden superglobal
        if (!is_string($node->name) || !isset(self::FORBIDDEN_SUPERGLOBALS[$node->name])) {
            return [];
        }

        $replacement = self::FORBIDDEN_SUPERGLOBALS[$node->name];
        $message = sprintf(
            'Direct access to $%s is forbidden in controllers. Use %s instead.',
            $node->name,
            $replacement
        );

        return [
            RuleErrorBuilder::message($message)
                ->identifier('openemr.noSuperGlobalsInControllers')
                ->tip('Use Symfony Request object: Request::createFromGlobals()')
                ->build()
        ];
    }
}
