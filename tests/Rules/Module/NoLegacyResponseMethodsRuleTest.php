<?php

/**
 * Test for NoLegacyResponseMethodsRule
 *
 * @package   OpenCoreEMR
 * @link      https://opencoreemr.com
 * @author    Michael A. Smith <michael@opencoreemr.com>
 * @copyright Copyright (c) 2026 OpenCoreEMR Inc
 * @license   https://github.com/openCoreEMR/openemr-phpstan-rules/blob/main/LICENSE GNU General Public License 3
 */

namespace OpenCoreEMR\PHPStan\Tests\Rules\Module;

use OpenCoreEMR\PHPStan\Rules\Module\NoLegacyResponseMethodsRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<NoLegacyResponseMethodsRule>
 */
class NoLegacyResponseMethodsRuleTest extends RuleTestCase
{
    protected function getRule(): Rule
    {
        return new NoLegacyResponseMethodsRule();
    }

    public function testLegacyResponseMethodsInController(): void
    {
        $tip = 'Controllers should return Response objects';
        $echoTip = 'Return new Response($content) or use JsonResponse';
        $this->analyse([__DIR__ . '/data/legacy-response-in-controller.php'], [
            [
                'Function header() is forbidden in controllers. Use RedirectResponse, Response, or JsonResponse.',
                12,
                $tip,
            ],
            [
                'Function http_response_code() is forbidden in controllers. Use Response constructor with status code.',
                17,
                $tip,
            ],
            [
                'die/exit is forbidden in controllers. Throw an exception instead.',
                22,
                $tip,
            ],
            [
                'die/exit is forbidden in controllers. Throw an exception instead.',
                27,
                $tip,
            ],
            [
                'Direct echo is forbidden in controllers. Use Response objects or Twig templates.',
                32,
                $echoTip,
            ],
        ]);
    }

    public function testLegacyResponseMethodsOutsideController(): void
    {
        $this->analyse([__DIR__ . '/data/legacy-response-outside-controller.php'], []);
    }
}
