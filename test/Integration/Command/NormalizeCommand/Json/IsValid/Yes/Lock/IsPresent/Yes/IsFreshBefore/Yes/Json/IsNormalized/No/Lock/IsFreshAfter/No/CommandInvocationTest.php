<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018-2024 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/composer-normalize
 */

namespace Ergebnis\Composer\Normalize\Test\Integration\Command\NormalizeCommand\Json\IsValid\Yes\Lock\IsPresent\Yes\IsFreshBefore\Yes\Json\IsNormalized\No\Lock\IsFreshAfter\No;

use Ergebnis\Composer\Normalize\Test;
use Symfony\Component\Console;

/**
 * @covers \Ergebnis\Composer\Normalize\Command\NormalizeCommand
 *
 * @uses \Ergebnis\Composer\Normalize\NormalizePlugin
 * @uses \Ergebnis\Composer\Normalize\Version
 */
final class CommandInvocationTest extends Test\Integration\Command\NormalizeCommand\AbstractTestCase
{
    /**
     * @dataProvider \Ergebnis\Composer\Normalize\Test\DataProvider\Command\NormalizeCommandProvider::commandInvocation
     */
    public function testSucceeds(Test\Util\CommandInvocation $commandInvocation): void
    {
        $scenario = self::createScenario(
            $commandInvocation,
            __DIR__ . '/fixture',
        );

        $initialState = $scenario->initialState();

        self::assertComposerJsonFileExists($initialState);
        self::assertComposerLockFileExists($initialState);
        self::assertComposerLockFileFresh($initialState);

        $application = self::createApplicationWithNormalizeCommandAsProvidedByNormalizePlugin();

        $input = new Console\Input\ArrayInput($scenario->consoleParameters());

        $output = new Console\Output\BufferedOutput();

        $exitCode = $application->run(
            $input,
            $output,
        );

        $display = $output->fetch();

        self::assertStringNotContainsString('A script named hello would override a Composer command and has been skipped', $display);

        $expected = \sprintf(
            'Successfully normalized %s.',
            $scenario->composerJsonFileReference(),
        );

        self::assertStringContainsString($expected, $display);
        self::assertExitCodeSame(0, $exitCode);

        $currentState = $scenario->currentState();

        self::assertComposerJsonFileModified($initialState, $currentState);
        self::assertComposerLockFileModified($initialState, $currentState);
        self::assertComposerLockFileFresh($currentState);
    }

    /**
     * @dataProvider \Ergebnis\Composer\Normalize\Test\DataProvider\Command\NormalizeCommandProvider::commandInvocation
     */
    public function testSucceedsWhenNoUpdateLockOptionIsUsed(Test\Util\CommandInvocation $commandInvocation): void
    {
        $scenario = self::createScenario(
            $commandInvocation,
            __DIR__ . '/fixture',
        );

        $initialState = $scenario->initialState();

        self::assertComposerJsonFileExists($initialState);
        self::assertComposerLockFileExists($initialState);
        self::assertComposerLockFileFresh($initialState);

        $application = self::createApplicationWithNormalizeCommandAsProvidedByNormalizePlugin();

        $output = new Console\Output\BufferedOutput();

        $input = new Console\Input\ArrayInput($scenario->consoleParametersWith([
            '--no-update-lock' => true,
        ]));

        $exitCode = $application->run(
            $input,
            $output,
        );

        $expected = \sprintf(
            'Successfully normalized %s.',
            $scenario->composerJsonFileReference(),
        );

        self::assertStringContainsString($expected, $output->fetch());
        self::assertExitCodeSame(0, $exitCode);

        $currentState = $scenario->currentState();

        self::assertComposerJsonFileModified($initialState, $currentState);
        self::assertComposerLockFileNotModified($initialState, $currentState);
        self::assertComposerLockFileNotFresh($currentState);
    }
}
