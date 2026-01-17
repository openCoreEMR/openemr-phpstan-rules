<?php

/**
 * Base test case for PHPStan rules
 *
 * @package   OpenCoreEMR
 * @link      https://opencoreemr.com
 * @author    Michael A. Smith <michael@opencoreemr.com>
 * @copyright Copyright (c) 2026 OpenCoreEMR Inc
 * @license   https://github.com/openCoreEMR/openemr-phpstan-rules/blob/main/LICENSE GNU General Public License 3
 */

namespace OpenCoreEMR\PHPStan\Tests;

use PHPStan\Testing\RuleTestCase as PHPStanRuleTestCase;

/**
 * Base test case that includes test-specific configuration.
 *
 * @template TRule of \PHPStan\Rules\Rule
 * @extends PHPStanRuleTestCase<TRule>
 */
abstract class RuleTestCase extends PHPStanRuleTestCase
{
    /**
     * @return string[]
     */
    public static function getAdditionalConfigFiles(): array
    {
        return [
            __DIR__ . '/phpstan-tests.neon',
        ];
    }
}
