<?php

/**
 * Test for NoCoversAnnotationOnClassRule
 *
 * @package   OpenCoreEMR
 * @link      https://opencoreemr.com
 * @author    Michael A. Smith <michael@opencoreemr.com>
 * @copyright Copyright (c) 2026 OpenCoreEMR Inc
 * @license   https://github.com/openCoreEMR/openemr-phpstan-rules/blob/main/LICENSE GNU General Public License 3
 */

namespace OpenCoreEMR\PHPStan\Tests\Rules\Testing;

use OpenCoreEMR\PHPStan\Rules\Testing\NoCoversAnnotationOnClassRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<NoCoversAnnotationOnClassRule>
 */
class NoCoversAnnotationOnClassRuleTest extends RuleTestCase
{
    protected function getRule(): Rule
    {
        return new NoCoversAnnotationOnClassRule();
    }

    public function testCoversAnnotationOnClass(): void
    {
        $this->analyse([__DIR__ . '/data/covers-on-class.php'], [
            [
                'Please do not use the @covers annotation. It excludes transitively used code from coverage reports, resulting in incomplete coverage information.',
                10,
            ],
        ]);
    }

    public function testNoCoversAnnotationOnClass(): void
    {
        $this->analyse([__DIR__ . '/data/no-covers-on-class.php'], []);
    }
}
