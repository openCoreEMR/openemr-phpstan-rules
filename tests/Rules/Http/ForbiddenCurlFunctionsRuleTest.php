<?php

/**
 * Test for ForbiddenCurlFunctionsRule
 *
 * @package   OpenCoreEMR
 * @link      https://opencoreemr.com
 * @author    Michael A. Smith <michael@opencoreemr.com>
 * @copyright Copyright (c) 2026 OpenCoreEMR Inc
 * @license   https://github.com/openCoreEMR/openemr-phpstan-rules/blob/main/LICENSE GNU General Public License 3
 */

namespace OpenCoreEMR\PHPStan\Tests\Rules\Http;

use OpenCoreEMR\PHPStan\Rules\Http\ForbiddenCurlFunctionsRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<ForbiddenCurlFunctionsRule>
 */
class ForbiddenCurlFunctionsRuleTest extends RuleTestCase
{
    protected function getRule(): Rule
    {
        return new ForbiddenCurlFunctionsRule();
    }

    public function testForbiddenCurlFunctions(): void
    {
        $tip = 'See https://www.php-fig.org/psr/psr-18/ for PSR-18 HTTP Client information';
        $this->analyse([__DIR__ . '/data/forbidden-curl-functions.php'], [
            [
                'Raw curl function curl_init() is forbidden. Use a PSR-18 HTTP client instead.',
                5,
                $tip,
            ],
            [
                'Raw curl function curl_setopt() is forbidden. Use a PSR-18 HTTP client instead.',
                6,
                $tip,
            ],
            [
                'Raw curl function curl_exec() is forbidden. Use a PSR-18 HTTP client instead.',
                7,
                $tip,
            ],
            [
                'Raw curl function curl_error() is forbidden. Use a PSR-18 HTTP client instead.',
                8,
                $tip,
            ],
            [
                'Raw curl function curl_getinfo() is forbidden. Use a PSR-18 HTTP client instead.',
                9,
                $tip,
            ],
            [
                'Raw curl function curl_close() is forbidden. Use a PSR-18 HTTP client instead.',
                10,
                $tip,
            ],
        ]);
    }

    public function testAllowedHttpFunctions(): void
    {
        $this->analyse([__DIR__ . '/data/allowed-http-functions.php'], []);
    }
}
