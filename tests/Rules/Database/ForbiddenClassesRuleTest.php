<?php

/**
 * Test for ForbiddenClassesRule
 *
 * @package   OpenCoreEMR
 * @link      https://opencoreemr.com
 * @author    Michael A. Smith <michael@opencoreemr.com>
 * @copyright Copyright (c) 2026 OpenCoreEMR Inc
 * @license   https://github.com/openCoreEMR/openemr-phpstan-rules/blob/main/LICENSE GNU General Public License 3
 */

namespace OpenCoreEMR\PHPStan\Tests\Rules\Database;

use OpenCoreEMR\PHPStan\Rules\Database\ForbiddenClassesRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<ForbiddenClassesRule>
 */
class ForbiddenClassesRuleTest extends RuleTestCase
{
    protected function getRule(): Rule
    {
        return new ForbiddenClassesRule();
    }

    public function testForbiddenClasses(): void
    {
        $tip = 'See src/Common/Database/QueryUtils.php for modern database patterns';
        $this->analyse([__DIR__ . '/data/forbidden-classes.php'], [
            [
                'Laminas-DB class "Laminas\Db\Adapter\Adapter" is deprecated. Use QueryUtils or DatabaseQueryTrait instead.',
                5,
                $tip,
            ],
            [
                'Laminas-DB class "Laminas\Db\Sql\Select" is deprecated. Use QueryUtils or DatabaseQueryTrait instead.',
                6,
                $tip,
            ],
            [
                'Laminas-DB class "Laminas\Db\TableGateway\TableGateway" is deprecated. Use QueryUtils or DatabaseQueryTrait instead.',
                7,
                $tip,
            ],
        ]);
    }

    public function testAllowedClasses(): void
    {
        $this->analyse([__DIR__ . '/data/allowed-classes.php'], []);
    }
}
