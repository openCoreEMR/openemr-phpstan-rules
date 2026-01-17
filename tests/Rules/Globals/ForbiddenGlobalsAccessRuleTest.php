<?php

/**
 * Test for ForbiddenGlobalsAccessRule
 *
 * @package   OpenCoreEMR
 * @link      https://opencoreemr.com
 * @author    Michael A. Smith <michael@opencoreemr.com>
 * @copyright Copyright (c) 2026 OpenCoreEMR Inc
 * @license   https://github.com/openCoreEMR/openemr-phpstan-rules/blob/main/LICENSE GNU General Public License 3
 */

namespace OpenCoreEMR\PHPStan\Tests\Rules\Globals;

use OpenCoreEMR\PHPStan\Rules\Globals\ForbiddenGlobalsAccessRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<ForbiddenGlobalsAccessRule>
 */
class ForbiddenGlobalsAccessRuleTest extends RuleTestCase
{
    protected function getRule(): Rule
    {
        return new ForbiddenGlobalsAccessRule();
    }

    public function testForbiddenGlobalsAccess(): void
    {
        $tip = 'For encrypted values, OEGlobalsBag handles decryption automatically. See src/Core/OEGlobalsBag.php';
        $this->analyse([__DIR__ . '/data/forbidden-globals.php'], [
            [
                'Direct access to $GLOBALS is forbidden. Use OEGlobalsBag::getInstance()->get() instead.',
                5,
                $tip,
            ],
            [
                'Direct access to $GLOBALS is forbidden. Use OEGlobalsBag::getInstance()->get() instead.',
                6,
                $tip,
            ],
            [
                'Direct access to $GLOBALS is forbidden. Use OEGlobalsBag::getInstance()->get() instead.',
                12,
                $tip,
            ],
        ]);
    }

    public function testAllowedGlobalsAccess(): void
    {
        $this->analyse([__DIR__ . '/data/allowed-globals.php'], []);
    }
}
