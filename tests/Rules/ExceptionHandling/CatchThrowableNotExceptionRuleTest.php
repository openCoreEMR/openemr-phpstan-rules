<?php

/**
 * Test for CatchThrowableNotExceptionRule
 *
 * @package   OpenCoreEMR
 * @link      https://opencoreemr.com
 * @author    Michael A. Smith <michael@opencoreemr.com>
 * @copyright Copyright (c) 2026 OpenCoreEMR Inc
 * @license   https://github.com/openCoreEMR/openemr-phpstan-rules/blob/main/LICENSE GNU General Public License 3
 */

namespace OpenCoreEMR\PHPStan\Tests\Rules\ExceptionHandling;

use OpenCoreEMR\PHPStan\Rules\ExceptionHandling\CatchThrowableNotExceptionRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<CatchThrowableNotExceptionRule>
 */
class CatchThrowableNotExceptionRuleTest extends RuleTestCase
{
    protected function getRule(): Rule
    {
        return new CatchThrowableNotExceptionRule();
    }

    public function testCatchingException(): void
    {
        $tip = 'Change catch (\Exception $e) to catch (\Throwable $e)';
        $this->analyse([__DIR__ . '/data/catch-exception.php'], [
            [
                'Catch \Throwable instead of \Exception to handle both exceptions and errors (TypeError, ParseError, etc.).',
                7,
                $tip,
            ],
            [
                'Catch \Throwable instead of \Exception to handle both exceptions and errors (TypeError, ParseError, etc.).',
                13,
                $tip,
            ],
        ]);
    }

    public function testCatchingThrowable(): void
    {
        $this->analyse([__DIR__ . '/data/catch-throwable.php'], []);
    }
}
