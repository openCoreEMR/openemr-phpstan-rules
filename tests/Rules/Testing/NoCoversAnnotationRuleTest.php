<?php

/**
 * Test for NoCoversAnnotationRule
 *
 * @package   OpenCoreEMR
 * @link      https://opencoreemr.com
 * @author    Michael A. Smith <michael@opencoreemr.com>
 * @copyright Copyright (c) 2026 OpenCoreEMR Inc
 * @license   https://github.com/openCoreEMR/openemr-phpstan-rules/blob/main/LICENSE GNU General Public License 3
 */

namespace OpenCoreEMR\PHPStan\Tests\Rules\Testing;

use OpenCoreEMR\PHPStan\Rules\Testing\NoCoversAnnotationRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<NoCoversAnnotationRule>
 */
class NoCoversAnnotationRuleTest extends RuleTestCase
{
    protected function getRule(): Rule
    {
        return new NoCoversAnnotationRule();
    }

    public function testCoversAnnotationOnMethod(): void
    {
        $this->analyse([__DIR__ . '/data/covers-on-method.php'], [
            [
                'Please do not use the @covers annotation. It excludes transitively used code from coverage reports, resulting in incomplete coverage information.',
                12,
            ],
        ]);
    }

    public function testNoCoversAnnotation(): void
    {
        $this->analyse([__DIR__ . '/data/no-covers-annotation.php'], []);
    }
}
