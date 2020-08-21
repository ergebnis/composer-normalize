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

use Composer\Console\Application;
use Composer\Factory;
use Ergebnis\Composer\Normalize\Command\NormalizeCommand;
use Ergebnis\Composer\Normalize\NormalizePlugin;
use Ergebnis\Composer\Normalize\Test\Util\CommandInvocation;
use Ergebnis\Composer\Normalize\Test\Util\Directory;
use Ergebnis\Composer\Normalize\Test\Util\Scenario;
use Ergebnis\Composer\Normalize\Test\Util\State;
use Ergebnis\Json\Normalizer\Format\Formatter;
use Ergebnis\Json\Normalizer\Json;
use Ergebnis\Json\Normalizer\NormalizerInterface;
use Ergebnis\Json\Printer\Printer;
use Ergebnis\Test\Util\Helper;
use Localheinz\Diff;
use PHPUnit\Framework;
use Symfony\Component\Console;
use Symfony\Component\Filesystem;

/**
 * @internal
 *
 * @covers \Ergebnis\Composer\Normalize\Command\NormalizeCommand
 * @covers \Ergebnis\Composer\Normalize\NormalizePlugin
 */
final class NormalizeCommandTest extends Framework\TestCase
{
    use Helper;

    /**
     * @var string
     */
    private $currentWorkingDirectory;

    public static function tearDownAfterClass(): void
    {
        self::clearTemporaryDirectory();
    }

    protected function setUp(): void
    {
        $currentWorkingDirectory = \getcwd();

        if (false === $currentWorkingDirectory) {
            throw new \RuntimeException('Unable to determine current working directory.');
        }

        $this->currentWorkingDirectory = $currentWorkingDirectory;
    }

    protected function tearDown(): void
    {
        \chdir($this->currentWorkingDirectory);
    }

    /**
     * @dataProvider providerCommandInvocation
     *
     * @param CommandInvocation $commandInvocation
     */
    public function testFailsWhenIndentStyleIsUsedWithoutIndentSize(CommandInvocation $commandInvocation): void
    {
        $scenario = self::createScenario(
            $commandInvocation,
            __DIR__ . '/../../Fixture/json/valid/lock/not-present/json/not-yet-normalized'
        );

        $application = self::createApplicationWithNormalizeCommandAsProvidedByNormalizePlugin();

        $input = new Console\Input\ArrayInput($scenario->consoleParametersWith([
            '--indent-style' => self::faker()->randomElement([
                'space',
                'tab',
            ]),
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
     * @dataProvider providerCommandInvocation
     *
     * @param CommandInvocation $commandInvocation
     */
    public function testFailsWhenIndentSizeIsUsedWithoutIndentStyle(CommandInvocation $commandInvocation): void
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
     * @dataProvider providerCommandInvocation
     *
     * @param CommandInvocation $commandInvocation
     */
    public function testFailsWhenIndentStyleIsInvalid(CommandInvocation $commandInvocation): void
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
     * @dataProvider providerCommandInvocationAndInvalidIndentSize
     *
     * @param CommandInvocation $commandInvocation
     * @param string            $indentSize
     */
    public function testFailsWhenIndentSizeIsInvalid(CommandInvocation $commandInvocation, string $indentSize): void
    {
        $faker = self::faker();

        /** @var string $indentStyle */
        $indentStyle = $faker->randomElement([
            'space',
            'tab',
        ]);

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
     * @dataProvider providerCommandInvocation
     *
     * @param CommandInvocation $commandInvocation
     */
    public function testFailsWhenComposerJsonIsPresentButNotValidAccordingToLaxValidation(CommandInvocation $commandInvocation): void
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
        self::assertContains('does not match the expected JSON schema', $output->fetch());
        self::assertEquals($initialState, $scenario->currentState());
    }

    /**
     * @dataProvider providerCommandInvocation
     *
     * @param CommandInvocation $commandInvocation
     */
    public function testFailsWhenComposerJsonIsPresentAndValidAccordingToLaxValidationAndComposerLockIsNotPresentAndRuntimeExceptionIsThrownDuringNormalization(CommandInvocation $commandInvocation): void
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
            new Formatter(new Printer()),
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
     * @dataProvider providerCommandInvocation
     *
     * @param CommandInvocation $commandInvocation
     */
    public function testSucceedsWhenComposerJsonIsPresentAndValidAccordingToLaxValidationAndComposerLockIsNotPresentAndComposerJsonIsAlreadyNormalized(CommandInvocation $commandInvocation): void
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
     * @dataProvider providerCommandInvocation
     *
     * @param CommandInvocation $commandInvocation
     */
    public function testSucceedsWhenComposerJsonIsPresentAndValidAccordingToLaxValidationAndComposerLockIsNotPresentAndComposerJsonIsNotYetNormalized(CommandInvocation $commandInvocation): void
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
     * @dataProvider providerCommandInvocation
     *
     * @param CommandInvocation $commandInvocation
     */
    public function testSucceedsWhenComposerJsonIsPresentAndValidAccordingToLaxValidationAndComposerLockIsNotPresentAndComposerJsonIsNotYetNormalizedAndDiffOptionIsUsed(CommandInvocation $commandInvocation): void
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
     * @dataProvider providerCommandInvocation
     *
     * @param CommandInvocation $commandInvocation
     */
    public function testFailsWhenComposerJsonIsPresentAndValidAccordingToLaxValidationAndComposerLockIsNotPresentAndComposerJsonIsNotYetNormalizedAndDryRunOptionIsUsed(CommandInvocation $commandInvocation): void
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
     * @dataProvider providerCommandInvocationIndentSizeAndIndentStyle
     *
     * @param CommandInvocation $commandInvocation
     * @param int               $indentSize
     * @param string            $indentStyle
     */
    public function testSucceedsWhenComposerJsonIsPresentAndValidAccordingToLaxValidationAndComposerLockIsNotPresentAndComposerJsonIsNotYetNormalizedAndIndentSizeAndIndentStyleOptionsAreUsed(
        CommandInvocation $commandInvocation,
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
     * @dataProvider providerCommandInvocation
     *
     * @param CommandInvocation $commandInvocation
     */
    public function testSucceedsWhenComposerJsonIsPresentAndValidAccordingToLaxValidationAndComposerLockIsNotPresentAndComposerJsonIsNotYetNormalizedAndNoUpdateLockOptionIsUsed(CommandInvocation $commandInvocation): void
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
     * @dataProvider providerCommandInvocation
     *
     * @param CommandInvocation $commandInvocation
     */
    public function testFailsWhenComposerJsonIsPresentAndValidAccordingToLaxValidationAndComposerLockIsPresentButNotFreshBefore(CommandInvocation $commandInvocation): void
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
     * @dataProvider providerCommandInvocation
     *
     * @param CommandInvocation $commandInvocation
     */
    public function testSucceedsWhenComposerJsonIsPresentAndValidAccordingToLaxValidationAndComposerLockIsPresentButNotFreshBeforeNoCheckLockOptionIsUsedAndComposerJsonIsAlreadyNormalized(CommandInvocation $commandInvocation): void
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
     * @dataProvider providerCommandInvocation
     *
     * @param CommandInvocation $commandInvocation
     */
    public function testSucceedsWhenComposerJsonIsPresentAndValidAccordingToLaxValidationAndComposerLockIsPresentButNotFreshBeforeNoCheckLockOptionIsUsedAndComposerJsonIsNotYetNormalized(CommandInvocation $commandInvocation): void
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
     * @dataProvider providerCommandInvocation
     *
     * @param CommandInvocation $commandInvocation
     */
    public function testSucceedsWhenComposerJsonIsPresentAndValidAccordingToLaxValidationAndComposerLockIsPresentAndFreshBeforeAndComposerJsonIsAlreadyNormalized(CommandInvocation $commandInvocation): void
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
     * @dataProvider providerCommandInvocation
     *
     * @param CommandInvocation $commandInvocation
     */
    public function testSucceedsWhenComposerJsonIsPresentAndValidAccordingToLaxValidationAndComposerLockIsPresentAndFreshBeforeAndComposerJsonIsNotYetNormalizedAndComposerLockIsFreshAfter(CommandInvocation $commandInvocation): void
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
     * @dataProvider providerCommandInvocation
     *
     * @param CommandInvocation $commandInvocation
     */
    public function testSucceedsWhenComposerJsonIsPresentAndValidAccordingToLaxValidationAndComposerLockIsPresentAndFreshBeforeAndComposerJsonIsNotYetNormalizedAndComposerLockIsNotFreshAfter(CommandInvocation $commandInvocation): void
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
     * @dataProvider providerCommandInvocation
     *
     * @param CommandInvocation $commandInvocation
     */
    public function testFailsWhenComposerJsonIsPresentAndValidAccordingToLaxValidationAndComposerLockIsPresentAndFreshBeforeAndComposerJsonIsNotYetNormalizedAndDryRunOptionIsUsed(CommandInvocation $commandInvocation): void
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
     * @dataProvider providerCommandInvocation
     *
     * @param CommandInvocation $commandInvocation
     */
    public function testSucceedsWhenComposerJsonIsPresentAndValidAccordingToLaxValidationAndComposerLockIsPresentAndFreshBeforeAndComposerJsonIsNotYetNormalizedAndNoUpdateLockOptionIsUsed(CommandInvocation $commandInvocation): void
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

    /**
     * @return \Generator<array<CommandInvocation>>
     */
    public function providerCommandInvocation(): \Generator
    {
        foreach (self::commandInvocations() as $commandInvocation) {
            yield $commandInvocation->style() => [
                $commandInvocation,
            ];
        }
    }

    /**
     * @return \Generator<array{0: CommandInvocation, 1: string}>
     */
    public function providerCommandInvocationAndInvalidIndentSize(): \Generator
    {
        $faker = self::faker();

        /** @var int $numberGreaterThanZero */
        $numberGreaterThanZero = $faker->numberBetween(1);

        /** @var array<string> $indentSizes */
        $indentSizes = [
            'string-arbitrary' => $faker->sentence,
            'integer-zero-casted-to-string' => (string) 0,
            'integer-less-than-zero-casted-to-string' => (string) (-1 * $numberGreaterThanZero),
        ];

        foreach (self::commandInvocations() as $commandInvocation) {
            foreach ($indentSizes as $indentSizeKey => $indentSize) {
                $key = \sprintf(
                    '%s-indent-size-%s',
                    $commandInvocation->style(),
                    $indentSizeKey
                );

                yield $key => [
                    $commandInvocation,
                    $indentSize,
                ];
            }
        }
    }

    /**
     * @return \Generator<array{0: CommandInvocation, 1: int, 2: string}>
     */
    public function providerCommandInvocationIndentSizeAndIndentStyle(): \Generator
    {
        /** @var array<int> $indentSizes */
        $indentSizes = [
            1,
            self::faker()->numberBetween(2, 4),
        ];

        $indentStyles = [
            'space',
            'tab',
        ];

        foreach (self::commandInvocations() as $commandInvocation) {
            foreach ($indentSizes as $indentSize) {
                foreach ($indentStyles as $indentStyle) {
                    $key = \sprintf(
                        '%s-indent-size-%d-indent-style-%s',
                        $commandInvocation->style(),
                        (string) $indentSize,
                        $indentStyle
                    );

                    yield $key => [
                        $commandInvocation,
                        $indentSize,
                        $indentStyle,
                    ];
                }
            }
        }
    }

    private static function createScenario(CommandInvocation $commandInvocation, string $fixtureDirectory): Scenario
    {
        if (!\is_dir($fixtureDirectory)) {
            throw new \InvalidArgumentException(\sprintf(
                'Fixture directory "%s" does not exist',
                $fixtureDirectory
            ));
        }

        self::clearTemporaryDirectory();

        /** @var string $fixtureDirectory */
        $fixtureDirectory = \realpath($fixtureDirectory);

        $temporaryFixtureDirectory = \str_replace(
            self::fixtureDirectory(),
            self::temporaryDirectory(),
            $fixtureDirectory
        );

        self::copyFiles(
            $fixtureDirectory,
            $temporaryFixtureDirectory
        );

        $scenario = Scenario::fromCommandInvocationAndInitialState(
            $commandInvocation,
            State::fromDirectory(Directory::fromPath($temporaryFixtureDirectory))
        );

        if ($commandInvocation->is(CommandInvocation::inCurrentWorkingDirectory())) {
            \chdir($scenario->directory()->path());
        }

        return $scenario;
    }

    private static function createApplication(NormalizeCommand $command): Application
    {
        $application = new Application();

        $application->add($command);
        $application->setAutoExit(false);

        return $application;
    }

    private static function createApplicationWithNormalizeCommandAsProvidedByNormalizePlugin(): Application
    {
        $plugin = new NormalizePlugin();

        $commands = \array_filter($plugin->getCommands(), static function ($command): bool {
            return $command instanceof NormalizeCommand;
        });

        if (0 === \count($commands)) {
            throw new \RuntimeException(\sprintf(
                'Expected "%s" to provide an instance of "%s" as command.',
                NormalizePlugin::class,
                NormalizeCommand::class
            ));
        }

        /** @var NormalizeCommand $normalizeCommand */
        $normalizeCommand = \array_shift($commands);

        return self::createApplication($normalizeCommand);
    }

    /**
     * @return CommandInvocation[]
     */
    private static function commandInvocations(): array
    {
        return [
            CommandInvocation::inCurrentWorkingDirectory(),
            CommandInvocation::usingFileArgument(),
            CommandInvocation::usingWorkingDirectoryOption(),
        ];
    }

    private static function assertComposerJsonFileExists(State $state): void
    {
        self::assertFileExists($state->composerJsonFile()->path());
    }

    private static function assertComposerJsonFileModified(State $expected, State $actual): void
    {
        self::assertComposerJsonFileExists($actual);

        self::assertNotEquals(
            $expected->composerJsonFile()->contents(),
            $actual->composerJsonFile()->contents(),
            'Failed asserting that initial composer.json has been modified.'
        );
    }

    private static function assertComposerLockFileExists(State $state): void
    {
        self::assertFileExists($state->composerLockFile()->path());
    }

    private static function assertComposerLockFileNotExists(State $state): void
    {
        self::assertFileNotExists($state->composerLockFile()->path());
    }

    private static function assertComposerLockFileFresh(State $state): void
    {
        self::assertComposerJsonFileExists($state);
        self::assertComposerLockFileExists($state);

        $exitCode = self::validateComposer($state);

        self::assertSame(0, $exitCode, \sprintf(
            'Failed asserting that composer.lock is fresh in %s.',
            $state->directory()->path()
        ));
    }

    private static function assertComposerLockFileNotFresh(State $state): void
    {
        self::assertComposerJsonFileExists($state);
        self::assertComposerLockFileExists($state);

        $exitCode = self::validateComposer($state);

        self::assertNotSame(0, $exitCode, \sprintf(
            'Failed asserting that composer.lock is not fresh in %s.',
            $state->directory()->path()
        ));
    }

    private static function assertComposerLockFileModified(State $expected, State $actual): void
    {
        self::assertComposerLockFileExists($actual);

        self::assertJsonStringNotEqualsJsonString(
            self::normalizeLockFileContents($expected->composerLockFile()->contents()),
            self::normalizeLockFileContents($actual->composerLockFile()->contents()),
            'Failed asserting that initial composer.lock has been modified.'
        );
    }

    private static function assertComposerLockFileNotModified(State $expected, State $actual): void
    {
        self::assertComposerLockFileExists($actual);

        self::assertJsonStringEqualsJsonString(
            self::normalizeLockFileContents($expected->composerLockFile()->contents()),
            self::normalizeLockFileContents($actual->composerLockFile()->contents()),
            'Failed asserting that initial composer.lock has not been modified.'
        );
    }

    private static function normalizeLockFileContents(string $contents): string
    {
        $decoded = \json_decode(
            $contents,
            true
        );

        unset($decoded['plugin-api-version']);

        $normalized = \json_encode(
            $decoded,
            \JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES | \JSON_PRESERVE_ZERO_FRACTION
        );

        if (!\is_string($normalized)) {
            throw new \RuntimeException('Failed normalizing contents of lock file.');
        }

        return $normalized;
    }

    private static function clearTemporaryDirectory(): void
    {
        $fileSystem = new Filesystem\Filesystem();

        $fileSystem->remove(self::temporaryDirectory());
    }

    private static function copyFiles(string $sourceDirectory, string $targetDirectory): void
    {
        $fileSystem = new Filesystem\Filesystem();

        $fileSystem->mkdir($targetDirectory);
        $fileSystem->mirror(
            $sourceDirectory,
            $targetDirectory
        );
    }

    private static function fixtureDirectory(): string
    {
        /** @var string $projectDirectory */
        $projectDirectory = \realpath(__DIR__ . '/../../..');

        return \sprintf(
            '%s/test/Fixture',
            $projectDirectory
        );
    }

    private static function temporaryDirectory(): string
    {
        /** @var string $projectDirectory */
        $projectDirectory = \realpath(__DIR__ . '/../../..');

        return \sprintf(
            '%s/.build/fixture',
            $projectDirectory
        );
    }

    private static function assertExitCodeSame(int $expected, int $actual): void
    {
        self::assertSame($expected, $actual, \sprintf(
            'Failed asserting that exit code %d is identical to %d.',
            $actual,
            $expected
        ));
    }

    private static function validateComposer(State $state): int
    {
        $application = new Application();

        $application->setAutoExit(false);

        return $application->run(
            new Console\Input\ArrayInput([
                'command' => 'validate',
                '--no-check-publish' => true,
                '--working-dir' => $state->directory()->path(),
            ]),
            new Console\Output\BufferedOutput()
        );
    }
}
