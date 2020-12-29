<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018-2020 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/composer-normalize
 */

namespace Ergebnis\Composer\Normalize\Test\Integration\Command\NormalizeCommand\Json\Valid\Lock\NotPresent\Json\NotYetNormalized;

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
     * @dataProvider \Ergebnis\Composer\Normalize\Test\DataProvider\Command\NormalizeCommandProvider::commandInvocation()
     */
    public function testSucceeds(Util\CommandInvocation $commandInvocation): void
    {
        $scenario = self::createScenario(
            $commandInvocation,
            __DIR__ . '/fixture'
        );

        $initialState = $scenario->initialState();

        self::assertComposerJsonFileExists($initialState);
        self::assertComposerLockFileNotExists($initialState);

        $application = self::createApplicationWithNormalizeCommandAsProvidedByNormalizePlugin();

        $input = new Console\Input\ArrayInput($scenario->consoleParameters());

        $output = new Console\Output\BufferedOutput();

        $exitCode = $application->run(
            $input,
            $output
        );

        self::assertExitCodeSame(0, $exitCode);

        $expected = \sprintf(
            'Successfully normalized %s.',
            $scenario->composerJsonFileReference()
        );

        self::assertStringContainsString($expected, $output->fetch());

        $currentState = $scenario->currentState();

        self::assertComposerJsonFileModified($initialState, $currentState);
        self::assertComposerLockFileNotExists($currentState);
    }

    /**
     * @dataProvider \Ergebnis\Composer\Normalize\Test\DataProvider\Command\NormalizeCommandProvider::commandInvocation()
     */
    public function testSucceedsWhenDiffOptionIsUsed(Util\CommandInvocation $commandInvocation): void
    {
        $scenario = self::createScenario(
            $commandInvocation,
            __DIR__ . '/fixture'
        );

        $initialState = $scenario->initialState();

        self::assertComposerJsonFileExists($initialState);
        self::assertComposerLockFileNotExists($initialState);

        $application = self::createApplicationWithNormalizeCommandAsProvidedByNormalizePlugin();

        $input = new Console\Input\ArrayInput($scenario->consoleParametersWith([
            '--diff' => true,
        ]));

        $output = new Console\Output\BufferedOutput();

        $exitCode = $application->run(
            $input,
            $output
        );

        self::assertExitCodeSame(0, $exitCode);

        $expected = \sprintf(
            'Successfully normalized %s.',
            $scenario->composerJsonFileReference()
        );

        $renderedOutput = $output->fetch();

        self::assertStringContainsString($expected, $renderedOutput);
        self::assertStringContainsString('--- original', $renderedOutput);
        self::assertStringContainsString('+++ normalized', $renderedOutput);
        self::assertStringContainsString('---------- begin diff ----------', $renderedOutput);
        self::assertStringContainsString('----------- end diff -----------', $renderedOutput);

        $currentState = $scenario->currentState();

        self::assertComposerJsonFileModified($initialState, $currentState);
        self::assertComposerLockFileNotExists($currentState);
    }

    /**
     * @dataProvider \Ergebnis\Composer\Normalize\Test\DataProvider\Command\NormalizeCommandProvider::commandInvocation()
     */
    public function testFailsDryRunOptionIsUsed(Util\CommandInvocation $commandInvocation): void
    {
        $scenario = self::createScenario(
            $commandInvocation,
            __DIR__ . '/fixture'
        );

        $initialState = $scenario->initialState();

        self::assertComposerJsonFileExists($initialState);
        self::assertComposerLockFileNotExists($initialState);

        $application = self::createApplicationWithNormalizeCommandAsProvidedByNormalizePlugin();

        $input = new Console\Input\ArrayInput($scenario->consoleParametersWith([
            '--dry-run' => true,
        ]));

        $output = new Console\Output\BufferedOutput();

        $exitCode = $application->run(
            $input,
            $output
        );

        self::assertExitCodeSame(1, $exitCode);

        $renderedOutput = $output->fetch();

        $expected = \sprintf(
            '%s is not normalized.',
            $scenario->composerJsonFileReference()
        );

        self::assertStringContainsString($expected, $renderedOutput);
        self::assertStringContainsString('--- original', $renderedOutput);
        self::assertStringContainsString('+++ normalized', $renderedOutput);
        self::assertStringContainsString('---------- begin diff ----------', $renderedOutput);
        self::assertStringContainsString('----------- end diff -----------', $renderedOutput);
        self::assertEquals($initialState, $scenario->currentState());
    }

    /**
     * @dataProvider \Ergebnis\Composer\Normalize\Test\DataProvider\Command\NormalizeCommandProvider::commandInvocationIndentSizeAndIndentStyle
     */
    public function testSucceedsWhenIndentSizeAndIndentStyleOptionsAreUsed(
        Util\CommandInvocation $commandInvocation,
        int $indentSize,
        string $indentStyle
    ): void {
        $scenario = self::createScenario(
            $commandInvocation,
            __DIR__ . '/fixture'
        );

        $initialState = $scenario->initialState();

        self::assertComposerJsonFileExists($initialState);
        self::assertComposerLockFileNotExists($initialState);

        $application = self::createApplicationWithNormalizeCommandAsProvidedByNormalizePlugin();

        $input = new Console\Input\ArrayInput($scenario->consoleParametersWith([
            '--indent-size' => (string) $indentSize,
            '--indent-style' => $indentStyle,
        ]));

        $output = new Console\Output\BufferedOutput();

        $exitCode = $application->run(
            $input,
            $output
        );

        self::assertExitCodeSame(0, $exitCode);

        $expected = \sprintf(
            'Successfully normalized %s.',
            $scenario->composerJsonFileReference()
        );

        self::assertStringContainsString($expected, $output->fetch());

        $currentState = $scenario->currentState();

        self::assertComposerJsonFileModified($initialState, $currentState);
        self::assertComposerLockFileNotExists($currentState);
    }

    /**
     * @dataProvider \Ergebnis\Composer\Normalize\Test\DataProvider\Command\NormalizeCommandProvider::commandInvocation()
     */
    public function testSucceedsNoUpdateLockOptionIsUsed(Util\CommandInvocation $commandInvocation): void
    {
        $scenario = self::createScenario(
            $commandInvocation,
            __DIR__ . '/fixture'
        );

        $initialState = $scenario->initialState();

        self::assertComposerJsonFileExists($initialState);
        self::assertComposerLockFileNotExists($initialState);

        $application = self::createApplicationWithNormalizeCommandAsProvidedByNormalizePlugin();

        $input = new Console\Input\ArrayInput($scenario->consoleParametersWith([
            '--no-update-lock' => true,
        ]));

        $output = new Console\Output\BufferedOutput();

        $exitCode = $application->run(
            $input,
            $output
        );

        self::assertExitCodeSame(0, $exitCode);

        $expected = \sprintf(
            'Successfully normalized %s.',
            $scenario->composerJsonFileReference()
        );

        self::assertStringContainsString($expected, $output->fetch());

        $currentState = $scenario->currentState();

        self::assertComposerJsonFileModified($initialState, $currentState);
        self::assertComposerLockFileNotExists($initialState);
    }
}
