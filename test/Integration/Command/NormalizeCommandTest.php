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

namespace Ergebnis\Composer\Normalize\Test\Integration\Command;

use Composer\Factory;
use Ergebnis\Composer\Normalize\Command\NormalizeCommand;
use Ergebnis\Composer\Normalize\Test;
use Ergebnis\Json\Normalizer\Format;
use Ergebnis\Json\Normalizer\Json;
use Ergebnis\Json\Normalizer\NormalizerInterface;
use Ergebnis\Json\Printer\Printer;
use Localheinz\Diff;
use Symfony\Component\Console;

/**
 * @internal
 *
 * @covers \Ergebnis\Composer\Normalize\Command\NormalizeCommand
 * @covers \Ergebnis\Composer\Normalize\NormalizePlugin
 */
final class NormalizeCommandTest extends AbstractTestCase
{
    /**
     * @dataProvider \Ergebnis\Composer\Normalize\Test\DataProvider\Command\NormalizeCommandProvider::commandInvocation()
     */
    public function testFailsWhenIndentStyleIsUsedWithoutIndentSize(Test\Util\CommandInvocation $commandInvocation): void
    {
        $scenario = self::createScenario(
            $commandInvocation,
            __DIR__ . '/../../Fixture/json/valid/lock/not-present/json/not-yet-normalized'
        );

        $application = self::createApplicationWithNormalizeCommandAsProvidedByNormalizePlugin();

        $input = new Console\Input\ArrayInput($scenario->consoleParametersWith([
            '--indent-style' => self::faker()->randomElement(\array_keys(Format\Indent::CHARACTERS)),
        ]));

        $output = new Console\Output\BufferedOutput();

        $exitCode = $application->run(
            $input,
            $output
        );

        self::assertExitCodeSame(1, $exitCode);
        self::assertStringContainsString('When using the indent-style option, an indent size needs to be specified using the indent-size option.', $output->fetch());
        self::assertEquals($scenario->initialState(), $scenario->currentState());
    }

    /**
     * @dataProvider \Ergebnis\Composer\Normalize\Test\DataProvider\Command\NormalizeCommandProvider::commandInvocation()
     */
    public function testFailsWhenIndentSizeIsUsedWithoutIndentStyle(Test\Util\CommandInvocation $commandInvocation): void
    {
        $scenario = self::createScenario(
            $commandInvocation,
            __DIR__ . '/../../Fixture/json/valid/lock/not-present/json/not-yet-normalized'
        );

        $application = self::createApplicationWithNormalizeCommandAsProvidedByNormalizePlugin();

        $input = new Console\Input\ArrayInput($scenario->consoleParametersWith([
            '--indent-size' => (string) self::faker()->numberBetween(1, 4),
        ]));

        $output = new Console\Output\BufferedOutput();

        $exitCode = $application->run(
            $input,
            $output
        );

        self::assertExitCodeSame(1, $exitCode);
        self::assertStringContainsString('When using the indent-size option, an indent style (one of "space", "tab") needs to be specified using the indent-style option.', $output->fetch());
        self::assertEquals($scenario->initialState(), $scenario->currentState());
    }

    /**
     * @dataProvider \Ergebnis\Composer\Normalize\Test\DataProvider\Command\NormalizeCommandProvider::commandInvocation()
     */
    public function testFailsWhenIndentStyleIsInvalid(Test\Util\CommandInvocation $commandInvocation): void
    {
        $faker = self::faker();

        /** @var string $indentStyle */
        $indentStyle = $faker->sentence;

        $scenario = self::createScenario(
            $commandInvocation,
            __DIR__ . '/../../Fixture/json/valid/lock/not-present/json/not-yet-normalized'
        );

        $application = self::createApplicationWithNormalizeCommandAsProvidedByNormalizePlugin();

        $input = new Console\Input\ArrayInput($scenario->consoleParametersWith([
            '--indent-size' => (string) $faker->numberBetween(1, 4),
            '--indent-style' => $indentStyle,
        ]));

        $output = new Console\Output\BufferedOutput();

        $exitCode = $application->run(
            $input,
            $output
        );

        self::assertExitCodeSame(1, $exitCode);

        $expected = \sprintf(
            'Indent style needs to be one of "space", "tab", but "%s" is not.',
            $indentStyle
        );

        self::assertStringContainsString($expected, $output->fetch());
        self::assertEquals($scenario->initialState(), $scenario->currentState());
    }

    /**
     * @dataProvider \Ergebnis\Composer\Normalize\Test\DataProvider\Command\NormalizeCommandProvider::commandInvocationAndInvalidIndentSize
     */
    public function testFailsWhenIndentSizeIsInvalid(Test\Util\CommandInvocation $commandInvocation, string $indentSize): void
    {
        $faker = self::faker();

        /** @var string $indentStyle */
        $indentStyle = $faker->randomElement(\array_keys(Format\Indent::CHARACTERS));

        $scenario = self::createScenario(
            $commandInvocation,
            __DIR__ . '/../../Fixture/json/valid/lock/not-present/json/not-yet-normalized'
        );

        $application = self::createApplicationWithNormalizeCommandAsProvidedByNormalizePlugin();

        $input = new Console\Input\ArrayInput($scenario->consoleParametersWith([
            '--indent-size' => $indentSize,
            '--indent-style' => $indentStyle,
        ]));

        $output = new Console\Output\BufferedOutput();

        $exitCode = $application->run(
            $input,
            $output
        );

        self::assertExitCodeSame(1, $exitCode);

        $expected = \sprintf(
            'Indent size needs to be an integer greater than 0, but "%s" is not.',
            $indentSize
        );

        self::assertStringContainsString($expected, $output->fetch());
        self::assertEquals($scenario->initialState(), $scenario->currentState());
    }

    /**
     * @dataProvider \Ergebnis\Composer\Normalize\Test\DataProvider\Command\NormalizeCommandProvider::commandInvocation()
     */
    public function testFailsWhenComposerJsonIsPresentButNotValidAccordingToLaxValidation(Test\Util\CommandInvocation $commandInvocation): void
    {
        $scenario = self::createScenario(
            $commandInvocation,
            __DIR__ . '/../../Fixture/json/not-valid'
        );

        $initialState = $scenario->initialState();

        self::assertComposerJsonFileExists($initialState);
        self::assertComposerLockFileNotExists($initialState);

        $application = self::createApplicationWithNormalizeCommandAsProvidedByNormalizePlugin();

        $output = new Console\Output\BufferedOutput();

        $input = new Console\Input\ArrayInput($scenario->consoleParameters());

        $exitCode = $application->run(
            $input,
            $output
        );

        self::assertExitCodeSame(1, $exitCode);
        self::assertStringContainsString('does not match the expected JSON schema', $output->fetch());
        self::assertEquals($initialState, $scenario->currentState());
    }

    /**
     * @dataProvider \Ergebnis\Composer\Normalize\Test\DataProvider\Command\NormalizeCommandProvider::commandInvocation()
     */
    public function testFailsWhenComposerJsonIsPresentAndValidAccordingToLaxValidationAndComposerLockIsNotPresentAndRuntimeExceptionIsThrownDuringNormalization(Test\Util\CommandInvocation $commandInvocation): void
    {
        $scenario = self::createScenario(
            $commandInvocation,
            __DIR__ . '/../../Fixture/json/valid/lock/not-present/json/not-yet-normalized'
        );

        $initialState = $scenario->initialState();

        self::assertComposerJsonFileExists($initialState);
        self::assertComposerLockFileNotExists($initialState);

        /** @var string $exceptionMessage */
        $exceptionMessage = self::faker()->sentence;

        $application = self::createApplication(new NormalizeCommand(
            new Factory(),
            new class($exceptionMessage) implements NormalizerInterface {
                /**
                 * @var string
                 */
                private $exceptionMessage;

                public function __construct(string $exceptionMessage)
                {
                    $this->exceptionMessage = $exceptionMessage;
                }

                public function normalize(Json $json): Json
                {
                    throw new \RuntimeException($this->exceptionMessage);
                }
            },
            new Format\Formatter(new Printer()),
            new Diff\Differ(new Diff\Output\StrictUnifiedDiffOutputBuilder([
                'fromFile' => 'original',
                'toFile' => 'normalized',
            ]))
        ));

        $input = new Console\Input\ArrayInput($scenario->consoleParameters());

        $output = new Console\Output\BufferedOutput();

        $exitCode = $application->run(
            $input,
            $output
        );

        self::assertExitCodeSame(1, $exitCode);
        self::assertStringContainsString($exceptionMessage, $output->fetch());
        self::assertEquals($initialState, $scenario->currentState());
    }

    /**
     * @dataProvider \Ergebnis\Composer\Normalize\Test\DataProvider\Command\NormalizeCommandProvider::commandInvocation()
     */
    public function testSucceedsWhenComposerJsonIsPresentAndValidAccordingToLaxValidationAndComposerLockIsNotPresentAndComposerJsonIsAlreadyNormalized(Test\Util\CommandInvocation $commandInvocation): void
    {
        $scenario = self::createScenario(
            $commandInvocation,
            __DIR__ . '/../../Fixture/json/valid/lock/not-present/json/already-normalized'
        );

        $initialState = $scenario->initialState();

        self::assertComposerJsonFileExists($initialState);
        self::assertComposerLockFileNotExists($initialState);

        $application = self::createApplicationWithNormalizeCommandAsProvidedByNormalizePlugin();

        $output = new Console\Output\BufferedOutput();

        $input = new Console\Input\ArrayInput($scenario->consoleParameters());

        $exitCode = $application->run(
            $input,
            $output
        );

        self::assertExitCodeSame(0, $exitCode);

        $expected = \sprintf(
            '%s is already normalized.',
            $scenario->composerJsonFileReference()
        );

        self::assertStringContainsString($expected, $output->fetch());
        self::assertEquals($initialState, $scenario->currentState());
    }

    /**
     * @dataProvider \Ergebnis\Composer\Normalize\Test\DataProvider\Command\NormalizeCommandProvider::commandInvocation()
     */
    public function testSucceedsWhenComposerJsonIsPresentAndValidAccordingToLaxValidationAndComposerLockIsNotPresentAndComposerJsonIsNotYetNormalized(Test\Util\CommandInvocation $commandInvocation): void
    {
        $scenario = self::createScenario(
            $commandInvocation,
            __DIR__ . '/../../Fixture/json/valid/lock/not-present/json/not-yet-normalized'
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
    public function testSucceedsWhenComposerJsonIsPresentAndValidAccordingToLaxValidationAndComposerLockIsNotPresentAndComposerJsonIsNotYetNormalizedAndDiffOptionIsUsed(Test\Util\CommandInvocation $commandInvocation): void
    {
        $scenario = self::createScenario(
            $commandInvocation,
            __DIR__ . '/../../Fixture/json/valid/lock/not-present/json/not-yet-normalized'
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
    public function testFailsWhenComposerJsonIsPresentAndValidAccordingToLaxValidationAndComposerLockIsNotPresentAndComposerJsonIsNotYetNormalizedAndDryRunOptionIsUsed(Test\Util\CommandInvocation $commandInvocation): void
    {
        $scenario = self::createScenario(
            $commandInvocation,
            __DIR__ . '/../../Fixture/json/valid/lock/not-present/json/not-yet-normalized'
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
    public function testSucceedsWhenComposerJsonIsPresentAndValidAccordingToLaxValidationAndComposerLockIsNotPresentAndComposerJsonIsNotYetNormalizedAndIndentSizeAndIndentStyleOptionsAreUsed(
        Test\Util\CommandInvocation $commandInvocation,
        int $indentSize,
        string $indentStyle
    ): void {
        $scenario = self::createScenario(
            $commandInvocation,
            __DIR__ . '/../../Fixture/json/valid/lock/not-present/json/not-yet-normalized'
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
    public function testSucceedsWhenComposerJsonIsPresentAndValidAccordingToLaxValidationAndComposerLockIsNotPresentAndComposerJsonIsNotYetNormalizedAndNoUpdateLockOptionIsUsed(Test\Util\CommandInvocation $commandInvocation): void
    {
        $scenario = self::createScenario(
            $commandInvocation,
            __DIR__ . '/../../Fixture/json/valid/lock/not-present/json/not-yet-normalized'
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

    /**
     * @dataProvider \Ergebnis\Composer\Normalize\Test\DataProvider\Command\NormalizeCommandProvider::commandInvocation()
     */
    public function testFailsWhenComposerJsonIsPresentAndValidAccordingToLaxValidationAndComposerLockIsPresentButNotFreshBefore(Test\Util\CommandInvocation $commandInvocation): void
    {
        $scenario = self::createScenario(
            $commandInvocation,
            __DIR__ . '/../../Fixture/json/valid/lock/present/lock/not-fresh-before/no-check-lock/false'
        );

        $initialState = $scenario->initialState();

        self::assertComposerJsonFileExists($initialState);
        self::assertComposerLockFileExists($initialState);
        self::assertComposerLockFileNotFresh($initialState);

        $application = self::createApplicationWithNormalizeCommandAsProvidedByNormalizePlugin();

        $input = new Console\Input\ArrayInput($scenario->consoleParameters());

        $output = new Console\Output\BufferedOutput();

        $exitCode = $application->run(
            $input,
            $output
        );

        self::assertExitCodeSame(1, $exitCode);
        self::assertStringContainsString('The lock file is not up to date with the latest changes in composer.json, it is recommended that you run `composer update --lock`.', $output->fetch());
        self::assertEquals($initialState, $scenario->currentState());
    }

    /**
     * @dataProvider \Ergebnis\Composer\Normalize\Test\DataProvider\Command\NormalizeCommandProvider::commandInvocation()
     */
    public function testSucceedsWhenComposerJsonIsPresentAndValidAccordingToLaxValidationAndComposerLockIsPresentButNotFreshBeforeNoCheckLockOptionIsUsedAndComposerJsonIsAlreadyNormalized(Test\Util\CommandInvocation $commandInvocation): void
    {
        $scenario = self::createScenario(
            $commandInvocation,
            __DIR__ . '/../../Fixture/json/valid/lock/present/lock/not-fresh-before/no-check-lock/true/json/already-normalized'
        );

        $initialState = $scenario->initialState();

        self::assertComposerJsonFileExists($initialState);
        self::assertComposerLockFileExists($initialState);
        self::assertComposerLockFileNotFresh($initialState);

        $application = self::createApplicationWithNormalizeCommandAsProvidedByNormalizePlugin();

        $input = new Console\Input\ArrayInput($scenario->consoleParametersWith([
            '--no-check-lock' => true,
        ]));

        $output = new Console\Output\BufferedOutput();

        $exitCode = $application->run(
            $input,
            $output
        );

        self::assertExitCodeSame(0, $exitCode);

        $expected = \sprintf(
            '%s is already normalized.',
            $scenario->composerJsonFileReference()
        );

        self::assertStringContainsString($expected, $output->fetch());
        self::assertEquals($initialState, $scenario->currentState());
    }

    /**
     * @dataProvider \Ergebnis\Composer\Normalize\Test\DataProvider\Command\NormalizeCommandProvider::commandInvocation()
     */
    public function testSucceedsWhenComposerJsonIsPresentAndValidAccordingToLaxValidationAndComposerLockIsPresentButNotFreshBeforeNoCheckLockOptionIsUsedAndComposerJsonIsNotYetNormalized(Test\Util\CommandInvocation $commandInvocation): void
    {
        $scenario = self::createScenario(
            $commandInvocation,
            __DIR__ . '/../../Fixture/json/valid/lock/present/lock/not-fresh-before/no-check-lock/true/json/not-yet-normalized'
        );

        $initialState = $scenario->initialState();

        self::assertComposerJsonFileExists($initialState);
        self::assertComposerLockFileExists($initialState);
        self::assertComposerLockFileNotFresh($initialState);

        $application = self::createApplicationWithNormalizeCommandAsProvidedByNormalizePlugin();

        $input = new Console\Input\ArrayInput($scenario->consoleParametersWith([
            '--no-check-lock' => true,
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
        self::assertComposerLockFileModified($initialState, $currentState);
        self::assertComposerLockFileFresh($currentState);
    }

    /**
     * @dataProvider \Ergebnis\Composer\Normalize\Test\DataProvider\Command\NormalizeCommandProvider::commandInvocation()
     */
    public function testSucceedsWhenComposerJsonIsPresentAndValidAccordingToLaxValidationAndComposerLockIsPresentAndFreshBeforeAndComposerJsonIsAlreadyNormalized(Test\Util\CommandInvocation $commandInvocation): void
    {
        $scenario = self::createScenario(
            $commandInvocation,
            __DIR__ . '/../../Fixture/json/valid/lock/present/lock/fresh-before/json/already-normalized'
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
            $output
        );

        self::assertExitCodeSame(0, $exitCode);

        $expected = \sprintf(
            '%s is already normalized.',
            $scenario->composerJsonFileReference()
        );

        self::assertStringContainsString($expected, $output->fetch());
        self::assertEquals($initialState, $scenario->currentState());
    }

    /**
     * @dataProvider \Ergebnis\Composer\Normalize\Test\DataProvider\Command\NormalizeCommandProvider::commandInvocation()
     */
    public function testSucceedsWhenComposerJsonIsPresentAndValidAccordingToLaxValidationAndComposerLockIsPresentAndFreshBeforeAndComposerJsonIsNotYetNormalizedAndComposerLockIsFreshAfter(Test\Util\CommandInvocation $commandInvocation): void
    {
        $scenario = self::createScenario(
            $commandInvocation,
            __DIR__ . '/../../Fixture/json/valid/lock/present/lock/fresh-before/json/not-yet-normalized/lock/fresh-after'
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
        self::assertComposerLockFileNotModified($initialState, $currentState);
    }

    /**
     * @dataProvider \Ergebnis\Composer\Normalize\Test\DataProvider\Command\NormalizeCommandProvider::commandInvocation()
     */
    public function testSucceedsWhenComposerJsonIsPresentAndValidAccordingToLaxValidationAndComposerLockIsPresentAndFreshBeforeAndComposerJsonIsNotYetNormalizedAndComposerLockIsNotFreshAfter(Test\Util\CommandInvocation $commandInvocation): void
    {
        $scenario = self::createScenario(
            $commandInvocation,
            __DIR__ . '/../../Fixture/json/valid/lock/present/lock/fresh-before/json/not-yet-normalized/lock/not-fresh-after'
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
            $output
        );

        self::assertExitCodeSame(0, $exitCode);

        $display = $output->fetch();

        self::assertStringNotContainsString('A script named hello would override a Composer command and has been skipped', $display);

        $expected = \sprintf(
            'Successfully normalized %s.',
            $scenario->composerJsonFileReference()
        );

        self::assertStringContainsString($expected, $display);

        $currentState = $scenario->currentState();

        self::assertComposerJsonFileModified($initialState, $currentState);
        self::assertComposerLockFileModified($initialState, $currentState);
        self::assertComposerLockFileFresh($currentState);
    }

    /**
     * @dataProvider \Ergebnis\Composer\Normalize\Test\DataProvider\Command\NormalizeCommandProvider::commandInvocation()
     */
    public function testFailsWhenComposerJsonIsPresentAndValidAccordingToLaxValidationAndComposerLockIsPresentAndFreshBeforeAndComposerJsonIsNotYetNormalizedAndDryRunOptionIsUsed(Test\Util\CommandInvocation $commandInvocation): void
    {
        $scenario = self::createScenario(
            $commandInvocation,
            __DIR__ . '/../../Fixture/json/valid/lock/present/lock/fresh-before/json/not-yet-normalized/lock/fresh-after'
        );

        $initialState = $scenario->initialState();

        self::assertComposerJsonFileExists($initialState);
        self::assertComposerLockFileExists($initialState);

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
        self::assertStringContainsString('---------- begin diff ----------', $renderedOutput);
        self::assertStringContainsString('----------- end diff -----------', $renderedOutput);
        self::assertEquals($initialState, $scenario->currentState());
    }

    /**
     * @dataProvider \Ergebnis\Composer\Normalize\Test\DataProvider\Command\NormalizeCommandProvider::commandInvocation()
     */
    public function testSucceedsWhenComposerJsonIsPresentAndValidAccordingToLaxValidationAndComposerLockIsPresentAndFreshBeforeAndComposerJsonIsNotYetNormalizedAndNoUpdateLockOptionIsUsed(Test\Util\CommandInvocation $commandInvocation): void
    {
        $scenario = self::createScenario(
            $commandInvocation,
            __DIR__ . '/../../Fixture/json/valid/lock/present/lock/fresh-before/json/not-yet-normalized/lock/not-fresh-after'
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
        self::assertComposerLockFileNotModified($initialState, $currentState);
        self::assertComposerLockFileNotFresh($currentState);
    }
}
