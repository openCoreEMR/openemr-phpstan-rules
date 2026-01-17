<?php

/**
 * Test for ForbiddenStaticMethodsRule
 *
 * @package   OpenCoreEMR
 * @link      https://opencoreemr.com
 * @author    Michael A. Smith <michael@opencoreemr.com>
 * @copyright Copyright (c) 2026 OpenCoreEMR Inc
 * @license   https://github.com/openCoreEMR/openemr-phpstan-rules/blob/main/LICENSE GNU General Public License 3
 */

namespace OpenCoreEMR\PHPStan\Tests\Rules\Database;

use OpenCoreEMR\PHPStan\Rules\Database\ForbiddenStaticMethodsRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<ForbiddenStaticMethodsRule>
 */
class ForbiddenStaticMethodsRuleTest extends RuleTestCase
{
    protected function getRule(): Rule
    {
        return new ForbiddenStaticMethodsRule();
    }

    public function testForbiddenStaticMethods(): void
    {
        $this->analyse([__DIR__ . '/data/forbidden-static-methods.php'], [
            [
                'OpenEMR\Common\Database\QueryUtils::startTransaction() is deprecated. Use QueryUtils::inTransaction() wrapper instead.',
                28,
            ],
            [
                'OpenEMR\Common\Database\QueryUtils::commitTransaction() is deprecated. Use QueryUtils::inTransaction() wrapper instead.',
                29,
            ],
            [
                'OpenEMR\Common\Database\QueryUtils::rollbackTransaction() is deprecated. Use QueryUtils::inTransaction() wrapper instead.',
                30,
            ],
        ]);
    }

    public function testAllowedStaticMethods(): void
    {
        $this->analyse([__DIR__ . '/data/allowed-static-methods.php'], []);
    }
}
