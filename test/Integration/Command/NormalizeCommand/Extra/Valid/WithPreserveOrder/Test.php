<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018-2022 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/composer-normalize
 */

namespace Ergebnis\Composer\Normalize\Test\Integration\Command\NormalizeCommand\Extra\Valid\WithPreserveOrder;

use Ergebnis\Composer\Normalize\Test\Integration;
use Ergebnis\Composer\Normalize\Test\Util;
use Symfony\Component\Console;

/**
 * @internal
 *
 * @covers \Ergebnis\Composer\Normalize\Command\NormalizeCommand
 * @covers \Ergebnis\Composer\Normalize\NormalizePlugin
 *
 * @uses \Ergebnis\Composer\Normalize\Version
 */
final class Test extends Integration\Command\NormalizeCommand\AbstractTestCase
{
    /**
     * @dataProvider \Ergebnis\Composer\Normalize\Test\DataProvider\Command\NormalizeCommandProvider::commandInvocationIndentSizeAndIndentStyle
     */
    public function testSucceeds(
        Util\CommandInvocation $commandInvocation,
        int $indentSize,
        string $indentStyle
    ): void {
        $scenario = self::createScenario(
            $commandInvocation,
            __DIR__ . '/fixture',
        );

        $initialState = $scenario->initialState();

        self::assertComposerJsonFileExists($initialState);
        self::assertComposerLockFileNotExists($initialState);

        $application = self::createApplicationWithNormalizeCommandAsProvidedByNormalizePlugin();

        $input = new Console\Input\ArrayInput($scenario->consoleParameters());
        $output = new Console\Output\BufferedOutput();

        $exitCode = $application->run(
            $input,
            $output,
        );

        self::assertExitCodeSame(0, $exitCode);

        $display = $output->fetch();

        $expected = \sprintf(
            'Successfully normalized %s.',
            $scenario->composerJsonFileReference(),
        );

        self::assertStringContainsString($expected, $display);

        $currentState = $scenario->currentState();

        self::assertComposerJsonFileModified($initialState, $currentState);
        self::assertComposerLockFileNotExists($currentState);

        $decoded = (array) \json_decode($currentState->composerJsonFile()->contents(), true, 32, \JSON_THROW_ON_ERROR);
        $require = (array) $decoded['require'];
        self::assertSame(['ext-json', 'php'], \array_keys($require));
        $extra = (array) $decoded['extra'];
        self::assertSame(['composer-normalize', 'other'], \array_keys($extra));
        $other = (array) $extra['other'];
        self::assertSame(['keep-unsorted', 'sort-this'], \array_keys($other));
        $keepUnsorted = (array) $other['keep-unsorted'];
        self::assertSame(['one', 'two', 'three', 'four'], $keepUnsorted);

        // FIXME: when ergebnis/json-normalizer has been upgraded to ^3.0, the following test can be uncommented / should work.
        // @see https://github.com/ergebnis/composer-normalize/pull/956
        // $sortThis = (array) $other['sort-this'];
        // self::assertSame(['first', 'last'], $sortThis);
    }
}
