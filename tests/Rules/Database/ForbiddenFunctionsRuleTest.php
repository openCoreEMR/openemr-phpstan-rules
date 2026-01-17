<?php

/**
 * Test for ForbiddenFunctionsRule
 *
 * @package   OpenCoreEMR
 * @link      https://opencoreemr.com
 * @author    Michael A. Smith <michael@opencoreemr.com>
 * @copyright Copyright (c) 2026 OpenCoreEMR Inc
 * @license   https://github.com/openCoreEMR/openemr-phpstan-rules/blob/main/LICENSE GNU General Public License 3
 */

namespace OpenCoreEMR\PHPStan\Tests\Rules\Database;

use OpenCoreEMR\PHPStan\Rules\Database\ForbiddenFunctionsRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<ForbiddenFunctionsRule>
 */
class ForbiddenFunctionsRuleTest extends RuleTestCase
{
    protected function getRule(): Rule
    {
        return new ForbiddenFunctionsRule();
    }

    public function testForbiddenFunctions(): void
    {
        $tip = 'Or use DatabaseQueryTrait in your class';
        $this->analyse([__DIR__ . '/data/forbidden-functions.php'], [
            [
                'Use QueryUtils::querySingleRow() or QueryUtils::fetchRecords() instead of sqlQuery().',
                5,
                $tip,
            ],
            [
                'Use QueryUtils::sqlStatementThrowException() or QueryUtils::fetchRecords() instead of sqlStatement().',
                6,
                $tip,
            ],
            [
                'Use QueryUtils::sqlInsert() instead of sqlInsert().',
                7,
                $tip,
            ],
            [
                'Use QueryUtils::fetchRecords() or QueryUtils::fetchArrayFromResultSet() instead of sqlFetchArray().',
                8,
                $tip,
            ],
            [
                'Use QueryUtils::startTransaction() instead of sqlBeginTrans().',
                9,
                $tip,
            ],
            [
                'Use QueryUtils::commitTransaction() instead of sqlCommitTrans().',
                10,
                $tip,
            ],
            [
                'Use QueryUtils::rollbackTransaction() instead of sqlRollbackTrans().',
                11,
                $tip,
            ],
        ]);
    }

    public function testAllowedFunctions(): void
    {
        $this->analyse([__DIR__ . '/data/allowed-functions.php'], []);
    }
}
