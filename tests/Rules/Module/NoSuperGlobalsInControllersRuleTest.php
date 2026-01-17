<?php

/**
 * Test for NoSuperGlobalsInControllersRule
 *
 * @package   OpenCoreEMR
 * @link      https://opencoreemr.com
 * @author    Michael A. Smith <michael@opencoreemr.com>
 * @copyright Copyright (c) 2026 OpenCoreEMR Inc
 * @license   https://github.com/openCoreEMR/openemr-phpstan-rules/blob/main/LICENSE GNU General Public License 3
 */

namespace OpenCoreEMR\PHPStan\Tests\Rules\Module;

use OpenCoreEMR\PHPStan\Rules\Module\NoSuperGlobalsInControllersRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<NoSuperGlobalsInControllersRule>
 */
class NoSuperGlobalsInControllersRuleTest extends RuleTestCase
{
    protected function getRule(): Rule
    {
        return new NoSuperGlobalsInControllersRule();
    }

    public function testSuperGlobalsInController(): void
    {
        $tip = 'Use Symfony Request object: Request::createFromGlobals()';
        $this->analyse([__DIR__ . '/data/superglobals-in-controller.php'], [
            [
                'Direct access to $_GET is forbidden in controllers. Use $request->query->get() instead.',
                12,
                $tip,
            ],
            [
                'Direct access to $_POST is forbidden in controllers. Use $request->request->get() instead.',
                13,
                $tip,
            ],
            [
                'Direct access to $_FILES is forbidden in controllers. Use $request->files->get() instead.',
                14,
                $tip,
            ],
            [
                'Direct access to $_SERVER is forbidden in controllers. Use $request->server->get() instead.',
                15,
                $tip,
            ],
        ]);
    }

    public function testSuperGlobalsOutsideController(): void
    {
        $this->analyse([__DIR__ . '/data/superglobals-outside-controller.php'], []);
    }
}
