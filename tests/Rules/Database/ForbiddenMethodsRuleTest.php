<?php

/**
 * Test for ForbiddenMethodsRule
 *
 * @package   OpenCoreEMR
 * @link      https://opencoreemr.com
 * @author    Michael A. Smith <michael@opencoreemr.com>
 * @copyright Copyright (c) 2026 OpenCoreEMR Inc
 * @license   https://github.com/openCoreEMR/openemr-phpstan-rules/blob/main/LICENSE GNU General Public License 3
 */

namespace OpenCoreEMR\PHPStan\Tests\Rules\Database;

use OpenCoreEMR\PHPStan\Rules\Database\ForbiddenMethodsRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<ForbiddenMethodsRule>
 */
class ForbiddenMethodsRuleTest extends RuleTestCase
{
    protected function getRule(): Rule
    {
        return new ForbiddenMethodsRule();
    }

    public function testForbiddenMethods(): void
    {
        $tip = 'Or use DatabaseQueryTrait in your class';
        $this->analyse([__DIR__ . '/data/forbidden-methods.php'], [
            [
                'GenID() is deprecated. Use QueryUtils::generateId() or QueryUtils::ediGenerateId() instead.',
                14,
                $tip,
            ],
        ]);
    }

    public function testAllowedMethods(): void
    {
        $this->analyse([__DIR__ . '/data/allowed-methods.php'], []);
    }
}
