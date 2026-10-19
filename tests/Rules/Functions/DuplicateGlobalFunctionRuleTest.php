<?php

/**
 * Test for DuplicateGlobalFunctionRule
 *
 * @package   OpenCoreEMR
 * @link      https://opencoreemr.com
 * @author    Michael A. Smith <michael@opencoreemr.com>
 * @copyright Copyright (c) 2026 OpenCoreEMR Inc
 * @license   https://github.com/openCoreEMR/openemr-phpstan-rules/blob/main/LICENSE GNU General Public License 3
 */

declare(strict_types=1);

namespace OpenCoreEMR\PHPStan\Tests\Rules\Functions;

use OpenCoreEMR\PHPStan\Rules\Functions\DuplicateGlobalFunctionRule;
use OpenCoreEMR\PHPStan\Rules\Functions\FunctionDeclarationCollector;
use OpenCoreEMR\PHPStan\Rules\Functions\FunctionExistsGuardCollector;
use PHPStan\File\RelativePathHelper;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<DuplicateGlobalFunctionRule>
 */
final class DuplicateGlobalFunctionRuleTest extends RuleTestCase
{
    private const MESSAGE = 'Function %s() is declared in more than one file. A request that loads two of them fails with "Cannot redeclare".';

    protected function getRule(): Rule
    {
        return new DuplicateGlobalFunctionRule(self::getContainer()->getByType(RelativePathHelper::class));
    }

    protected function getCollectors(): array
    {
        return [
            new FunctionDeclarationCollector(),
            new FunctionExistsGuardCollector(),
        ];
    }

    public function testReportsUnguardedCopiesAcrossFiles(): void
    {
        $this->assertSame(
            [
                'first-file.php:5 ' . sprintf(self::MESSAGE, 'sharedHelper'),
                'first-file.php:21 ' . sprintf(self::MESSAGE, 'MixedCaseName'),
                'first-file.php:29 ' . sprintf(self::MESSAGE, 'guardNamesAnotherFunction'),
                'second-file.php:5 ' . sprintf(self::MESSAGE, 'sharedHelper'),
                'second-file.php:15 ' . sprintf(self::MESSAGE, 'guardedHereOnly'),
                'second-file.php:19 ' . sprintf(self::MESSAGE, 'mixedcasename'),
                'second-file.php:24 ' . sprintf(self::MESSAGE, 'guardNamesAnotherFunction'),
            ],
            $this->reportedErrors([
                __DIR__ . '/data/first-file.php',
                __DIR__ . '/data/second-file.php',
            ])
        );
    }

    public function testTipNamesTheOtherDeclarations(): void
    {
        $tips = [];
        foreach ($this->gatherAnalyserErrors([__DIR__ . '/data/first-file.php', __DIR__ . '/data/second-file.php']) as $error) {
            $tips[basename($error->getFilePath()) . ':' . $error->getLine()] = $error->getTip() ?? '';
        }

        $this->assertStringContainsString('second-file.php:5.', $tips['first-file.php:5']);
        $this->assertStringContainsString('first-file.php:16 (guarded)', $tips['second-file.php:15']);
        $this->assertStringContainsString('Keep one declaration in a shared file', $tips['second-file.php:15']);
    }

    public function testReportsTheIdentifier(): void
    {
        $errors = $this->gatherAnalyserErrors([__DIR__ . '/data/first-file.php', __DIR__ . '/data/second-file.php']);

        $this->assertNotSame([], $errors);
        foreach ($errors as $error) {
            $this->assertSame('openemr.duplicateGlobalFunction', $error->getIdentifier());
        }
    }

    public function testNamespacedFunctionsCompareByFullyQualifiedName(): void
    {
        $this->assertSame(
            [
                'namespaced-copy.php:7 '
                    . sprintf(self::MESSAGE, 'OpenCoreEMR\PHPStan\Tests\Rules\Functions\Data\sharedHelper'),
                'namespaced-copy.php:11 '
                    . sprintf(self::MESSAGE, 'OpenCoreEMR\PHPStan\Tests\Rules\Functions\Data\onlyInFirstFile'),
                'namespaced-file.php:7 '
                    . sprintf(self::MESSAGE, 'OpenCoreEMR\PHPStan\Tests\Rules\Functions\Data\sharedHelper'),
            ],
            $this->reportedErrors([
                __DIR__ . '/data/first-file.php',
                __DIR__ . '/data/namespaced-file.php',
                __DIR__ . '/data/namespaced-copy.php',
            ])
        );
    }

    public function testSingleFileIsNotReported(): void
    {
        $this->analyse([__DIR__ . '/data/second-file.php'], []);
    }

    /**
     * @param list<string> $files
     * @return list<string>
     */
    private function reportedErrors(array $files): array
    {
        $reported = [];
        foreach ($this->gatherAnalyserErrors($files) as $error) {
            $reported[] = sprintf('%s:%d %s', basename($error->getFilePath()), $error->getLine(), $error->getMessage());
        }
        sort($reported, SORT_NATURAL);

        return $reported;
    }
}
