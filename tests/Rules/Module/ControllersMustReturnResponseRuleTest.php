<?php

/**
 * Test for ControllersMustReturnResponseRule
 *
 * @package   OpenCoreEMR
 * @link      https://opencoreemr.com
 * @author    Michael A. Smith <michael@opencoreemr.com>
 * @copyright Copyright (c) 2026 OpenCoreEMR Inc
 * @license   https://github.com/openCoreEMR/openemr-phpstan-rules/blob/main/LICENSE GNU General Public License 3
 */

namespace OpenCoreEMR\PHPStan\Tests\Rules\Module;

use OpenCoreEMR\PHPStan\Rules\Module\ControllersMustReturnResponseRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<ControllersMustReturnResponseRule>
 */
class ControllersMustReturnResponseRuleTest extends RuleTestCase
{
    protected function getRule(): Rule
    {
        return new ControllersMustReturnResponseRule();
    }

    public function testControllerWithoutReturnType(): void
    {
        $tip = 'Add return type: Response, JsonResponse, RedirectResponse, or BinaryFileResponse';
        $this->analyse([__DIR__ . '/data/controller-no-return-type.php'], [
            [
                'Controller method index() must declare a return type (Response or subclass).',
                10,
                $tip,
            ],
        ]);
    }

    public function testControllerWithVoidReturn(): void
    {
        $tip = 'Controllers should return Response, JsonResponse, RedirectResponse, or BinaryFileResponse';
        $this->analyse([__DIR__ . '/data/controller-void-return.php'], [
            [
                'Controller method index() must return Response object, not void.',
                10,
                $tip,
            ],
        ]);
    }

    public function testControllerWithProperReturn(): void
    {
        $this->analyse([__DIR__ . '/data/controller-proper-return.php'], []);
    }
}
