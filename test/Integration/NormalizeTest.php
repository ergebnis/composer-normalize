<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/localheinz/composer-normalize
 */

namespace Localheinz\Composer\Normalize\Test\Integration;

use Composer\Console\Application;
use Composer\Factory;
use Localheinz\Composer\Json\Normalizer\ComposerJsonNormalizer;
use Localheinz\Composer\Normalize\Command\NormalizeCommand;
use Localheinz\Composer\Normalize\Test\Util\CommandInvocation;
use Localheinz\Composer\Normalize\Test\Util\Directory;
use Localheinz\Composer\Normalize\Test\Util\Scenario;
use Localheinz\Composer\Normalize\Test\Util\State;
use Localheinz\Json\Normalizer\Format\Formatter;
use Localheinz\Json\Normalizer\Json;
use Localheinz\Json\Normalizer\NormalizerInterface;
use Localheinz\Test\Util\Helper;
use PHPUnit\Framework;
use SebastianBergmann\Diff\Differ;
use Symfony\Component\Console;
use Symfony\Component\Filesystem;

/**
 * @internal
 *
 * @covers \Localheinz\Composer\Normalize\Command\NormalizeCommand
 * @covers \Localheinz\Composer\Normalize\NormalizePlugin
 */
final class NormalizeTest extends Framework\TestCase
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
        $scenario = $this->createScenario(
            $commandInvocation,
            __DIR__ . '/../Fixture/json/valid/lock/not-present/json/not-yet-normalized'
        );

        $application = $this->createApplication(new NormalizeCommand(
            new Factory(),
            new ComposerJsonNormalizer(),
            new Formatter(),
            new Differ()
        ));

        $input = new Console\Input\ArrayInput($scenario->consoleParametersWith([
            '--indent-style' => $this->faker()->randomElement([
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
        self::assertContains('When using the indent-style option, an indent size needs to be specified using the indent-size option.', $output->fetch());
        self::assertEquals($scenario->initialState(), $scenario->currentState());
    }

    /**
     * @dataProvider providerCommandInvocation
     *
     * @param CommandInvocation $commandInvocation
     */
    public function testFailsWhenIndentSizeIsUsedWithoutIndentStyle(CommandInvocation $commandInvocation): void
    {
        $scenario = $this->createScenario(
            $commandInvocation,
            __DIR__ . '/../Fixture/json/valid/lock/not-present/json/not-yet-normalized'
        );

        $application = $this->createApplication(new NormalizeCommand(
            new Factory(),
            new ComposerJsonNormalizer(),
            new Formatter(),
            new Differ()
        ));

        $input = new Console\Input\ArrayInput($scenario->consoleParametersWith([
            '--indent-size' => (string) $this->faker()->numberBetween(1, 4),
        ]));

        $output = new Console\Output\BufferedOutput();

        $exitCode = $application->run(
            $input,
            $output
        );

        self::assertExitCodeSame(1, $exitCode);
        self::assertContains('When using the indent-size option, an indent style (one of "space", "tab") needs to be specified using the indent-style option.', $output->fetch());
        self::assertEquals($scenario->initialState(), $scenario->currentState());
    }

    /**
     * @dataProvider providerCommandInvocation
     *
     * @param CommandInvocation $commandInvocation
     */
    public function testFailsWhenIndentStyleIsInvalid(CommandInvocation $commandInvocation): void
    {
        $faker = $this->faker();

        $indentStyle = $faker->sentence;

        $scenario = $this->createScenario(
            $commandInvocation,
            __DIR__ . '/../Fixture/json/valid/lock/not-present/json/not-yet-normalized'
        );

        $application = $this->createApplication(new NormalizeCommand(
            new Factory(),
            new ComposerJsonNormalizer(),
            new Formatter(),
            new Differ()
        ));

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

        self::assertContains($expected, $output->fetch());
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
        $faker = $this->faker();

        $scenario = $this->createScenario(
            $commandInvocation,
            __DIR__ . '/../Fixture/json/valid/lock/not-present/json/not-yet-normalized'
        );

        $application = $this->createApplication(new NormalizeCommand(
            new Factory(),
            new ComposerJsonNormalizer(),
            new Formatter(),
            new Differ()
        ));

        $input = new Console\Input\ArrayInput($scenario->consoleParametersWith([
            '--indent-size' => $indentSize,
            '--indent-style' => $faker->randomElement([
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

        $expected = \sprintf(
            'Indent size needs to be an integer greater than 0, but "%s" is not.',
            $indentSize
        );

        self::assertContains($expected, $output->fetch());
        self::assertEquals($scenario->initialState(), $scenario->currentState());
    }

    /**
     * @dataProvider providerCommandInvocation
     *
     * @param CommandInvocation $commandInvocation
     */
    public function testFailsWhenComposerJsonIsPresentButNotValid(CommandInvocation $commandInvocation): void
    {
        $scenario = $this->createScenario(
            $commandInvocation,
            __DIR__ . '/../Fixture/json/not-valid'
        );

        $initialState = $scenario->initialState();

        self::assertComposerJsonFileExists($initialState);
        self::assertComposerLockFileNotExists($initialState);

        $application = $this->createApplication(new NormalizeCommand(
            new Factory(),
            new ComposerJsonNormalizer(),
            new Formatter(),
            new Differ()
        ));

        $output = new Console\Output\BufferedOutput();

        $input = new Console\Input\ArrayInput($scenario->consoleParameters());

        $exitCode = $application->run(
            $input,
            $output
        );

        self::assertExitCodeSame(1, $exitCode);
        self::assertContains('Original JSON is not valid according to schema "https://getcomposer.org/schema.json".', $output->fetch());
        self::assertEquals($initialState, $scenario->currentState());
    }

    /**
     * @dataProvider providerCommandInvocation
     *
     * @param CommandInvocation $commandInvocation
     */
    public function testFailsWhenComposerJsonIsPresentAndValidAndComposerLockIsNotPresentAndRuntimeExceptionIsThrownDuringNormalization(CommandInvocation $commandInvocation): void
    {
        $scenario = $this->createScenario(
            $commandInvocation,
            __DIR__ . '/../Fixture/json/valid/lock/not-present/json/not-yet-normalized'
        );

        $initialState = $scenario->initialState();

        self::assertComposerJsonFileExists($initialState);
        self::assertComposerLockFileNotExists($initialState);

        $exceptionMessage = $this->faker()->sentence;

        $application = $this->createApplication(new NormalizeCommand(
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
            new Formatter(),
            new Differ()
        ));

        $input = new Console\Input\ArrayInput($scenario->consoleParameters());

        $output = new Console\Output\BufferedOutput();

        $exitCode = $application->run(
            $input,
            $output
        );

        self::assertExitCodeSame(1, $exitCode);
        self::assertContains($exceptionMessage, $output->fetch());
        self::assertEquals($initialState, $scenario->currentState());
    }

    /**
     * @dataProvider providerCommandInvocationAndInvalidIndentSize
     *
     * @param CommandInvocation $commandInvocation
     */
    public function testSucceedsWhenComposerJsonIsPresentAndValidAndComposerLockIsNotPresentAndComposerJsonIsAlreadyNormalized(CommandInvocation $commandInvocation): void
    {
        $scenario = $this->createScenario(
            $commandInvocation,
            __DIR__ . '/../Fixture/json/valid/lock/not-present/json/already-normalized'
        );

        $initialState = $scenario->initialState();

        self::assertComposerJsonFileExists($initialState);
        self::assertComposerLockFileNotExists($initialState);

        $application = $this->createApplication(new NormalizeCommand(
            new Factory(),
            new ComposerJsonNormalizer(),
            new Formatter(),
            new Differ()
        ));

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

        self::assertContains($expected, $output->fetch());
        self::assertEquals($initialState, $scenario->currentState());
    }

    /**
     * @dataProvider providerCommandInvocation
     *
     * @param CommandInvocation $commandInvocation
     */
    public function testSucceedsWhenComposerJsonIsPresentAndValidAndComposerLockIsNotPresentAndComposerJsonIsNotYetNormalized(CommandInvocation $commandInvocation): void
    {
        $scenario = $this->createScenario(
            $commandInvocation,
            __DIR__ . '/../Fixture/json/valid/lock/not-present/json/not-yet-normalized'
        );

        $initialState = $scenario->initialState();

        self::assertComposerJsonFileExists($initialState);
        self::assertComposerLockFileNotExists($initialState);

        $application = $this->createApplication(new NormalizeCommand(
            new Factory(),
            new ComposerJsonNormalizer(),
            new Formatter(),
            new Differ()
        ));

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

        self::assertContains($expected, $output->fetch());

        $currentState = $scenario->currentState();

        self::assertComposerJsonFileModified($initialState, $currentState);
        self::assertComposerLockFileNotExists($currentState);
    }

    /**
     * @dataProvider providerCommandInvocation
     *
     * @param CommandInvocation $commandInvocation
     */
    public function testFailsWhenComposerJsonIsPresentAndValidAndComposerLockIsNotPresentAndComposerJsonIsNotYetNormalizedAndDryRunOptionIsUsed(CommandInvocation $commandInvocation): void
    {
        $scenario = $this->createScenario(
            $commandInvocation,
            __DIR__ . '/../Fixture/json/valid/lock/not-present/json/not-yet-normalized'
        );

        $initialState = $scenario->initialState();

        self::assertComposerJsonFileExists($initialState);
        self::assertComposerLockFileNotExists($initialState);

        $application = $this->createApplication(new NormalizeCommand(
            new Factory(),
            new ComposerJsonNormalizer(),
            new Formatter(),
            new Differ()
        ));

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

        self::assertContains($expected, $renderedOutput);
        self::assertContains('--- original', $renderedOutput);
        self::assertContains('+++ normalized', $renderedOutput);
        self::assertContains('---------- begin diff ----------', $renderedOutput);
        self::assertContains('----------- end diff -----------', $renderedOutput);
        self::assertEquals($initialState, $scenario->currentState());
    }

    /**
     * @dataProvider providerCommandInvocationIndentSizeAndIndentStyle
     *
     * @param CommandInvocation $commandInvocation
     * @param int               $indentSize
     * @param string            $indentStyle
     */
    public function testSucceedsWhenComposerJsonIsPresentAndValidAndComposerLockIsNotPresentAndComposerJsonIsNotYetNormalizedAndIndentSizeAndIndentStyleOptionsAreUsed(
        CommandInvocation $commandInvocation,
        int $indentSize,
        string $indentStyle
    ): void {
        $scenario = $this->createScenario(
            $commandInvocation,
            __DIR__ . '/../Fixture/json/valid/lock/not-present/json/not-yet-normalized'
        );

        $initialState = $scenario->initialState();

        self::assertComposerJsonFileExists($initialState);
        self::assertComposerLockFileNotExists($initialState);

        $application = $this->createApplication(new NormalizeCommand(
            new Factory(),
            new ComposerJsonNormalizer(),
            new Formatter(),
            new Differ()
        ));

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

        self::assertContains($expected, $output->fetch());

        $currentState = $scenario->currentState();

        self::assertComposerJsonFileModified($initialState, $currentState);
        self::assertComposerLockFileNotExists($currentState);
    }

    /**
     * @dataProvider providerCommandInvocation
     *
     * @param CommandInvocation $commandInvocation
     */
    public function testSucceedsWhenComposerJsonIsPresentAndValidAndComposerLockIsNotPresentAndComposerJsonIsNotYetNormalizedAndNoUpdateLockOptionIsUsed(CommandInvocation $commandInvocation): void
    {
        $scenario = $this->createScenario(
            $commandInvocation,
            __DIR__ . '/../Fixture/json/valid/lock/not-present/json/not-yet-normalized'
        );

        $initialState = $scenario->initialState();

        self::assertComposerJsonFileExists($initialState);
        self::assertComposerLockFileNotExists($initialState);

        $application = $this->createApplication(new NormalizeCommand(
            new Factory(),
            new ComposerJsonNormalizer(),
            new Formatter(),
            new Differ()
        ));

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

        self::assertContains($expected, $output->fetch());

        $currentState = $scenario->currentState();

        self::assertComposerJsonFileModified($initialState, $currentState);
        self::assertComposerLockFileNotExists($initialState);
    }

    /**
     * @dataProvider providerCommandInvocation
     *
     * @param CommandInvocation $commandInvocation
     */
    public function testFailsWhenComposerJsonIsPresentAndValidAndComposerLockIsPresentButNotFreshBefore(CommandInvocation $commandInvocation): void
    {
        $scenario = $this->createScenario(
            $commandInvocation,
            __DIR__ . '/../Fixture/json/valid/lock/present/lock/not-fresh-before'
        );

        $initialState = $scenario->initialState();

        self::assertComposerJsonFileExists($initialState);
        self::assertComposerLockFileExists($initialState);
        self::assertComposerLockFileNotFresh($initialState);

        $application = $this->createApplication(new NormalizeCommand(
            new Factory(),
            new ComposerJsonNormalizer(),
            new Formatter(),
            new Differ()
        ));

        $input = new Console\Input\ArrayInput($scenario->consoleParameters());

        $output = new Console\Output\BufferedOutput();

        $exitCode = $application->run(
            $input,
            $output
        );

        self::assertExitCodeSame(1, $exitCode);
        self::assertContains('The lock file is not up to date with the latest changes in composer.json, it is recommended that you run `composer update --lock`.', $output->fetch());
        self::assertEquals($initialState, $scenario->currentState());
    }

    /**
     * @dataProvider providerCommandInvocation
     *
     * @param CommandInvocation $commandInvocation
     */
    public function testSucceedsWhenComposerJsonIsPresentAndValidAndComposerLockIsPresentAndFreshBeforeAndComposerJsonIsAlreadyNormalized(CommandInvocation $commandInvocation): void
    {
        $scenario = $this->createScenario(
            $commandInvocation,
            __DIR__ . '/../Fixture/json/valid/lock/present/lock/fresh-before/json/already-normalized'
        );

        $initialState = $scenario->initialState();

        self::assertComposerJsonFileExists($initialState);
        self::assertComposerLockFileExists($initialState);
        self::assertComposerLockFileFresh($initialState);

        $application = $this->createApplication(new NormalizeCommand(
            new Factory(),
            new ComposerJsonNormalizer(),
            new Formatter(),
            new Differ()
        ));

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

        self::assertContains($expected, $output->fetch());
        self::assertEquals($initialState, $scenario->currentState());
    }

    /**
     * @dataProvider providerCommandInvocation
     *
     * @param CommandInvocation $commandInvocation
     */
    public function testSucceedsWhenComposerJsonIsPresentAndValidAndComposerLockIsPresentAndFreshBeforeAndComposerJsonIsNotYetNormalizedAndComposerLockIsFreshAfter(CommandInvocation $commandInvocation): void
    {
        $scenario = $this->createScenario(
            $commandInvocation,
            __DIR__ . '/../Fixture/json/valid/lock/present/lock/fresh-before/json/not-yet-normalized/lock/fresh-after'
        );

        $initialState = $scenario->initialState();

        self::assertComposerJsonFileExists($initialState);
        self::assertComposerLockFileExists($initialState);
        self::assertComposerLockFileFresh($initialState);

        $application = $this->createApplication(new NormalizeCommand(
            new Factory(),
            new ComposerJsonNormalizer(),
            new Formatter(),
            new Differ()
        ));

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

        self::assertContains($expected, $output->fetch());

        $currentState = $scenario->currentState();

        self::assertComposerJsonFileModified($initialState, $currentState);
        self::assertComposerLockFileNotModified($initialState, $currentState);
    }

    /**
     * @dataProvider providerCommandInvocation
     *
     * @param CommandInvocation $commandInvocation
     */
    public function testSucceedsWhenComposerJsonIsPresentAndValidAndComposerLockIsPresentAndFreshBeforeAndComposerJsonIsNotYetNormalizedAndComposerLockIsNotFreshAfter(CommandInvocation $commandInvocation): void
    {
        $scenario = $this->createScenario(
            $commandInvocation,
            __DIR__ . '/../Fixture/json/valid/lock/present/lock/fresh-before/json/not-yet-normalized/lock/not-fresh-after'
        );

        $initialState = $scenario->initialState();

        self::assertComposerJsonFileExists($initialState);
        self::assertComposerLockFileExists($initialState);
        self::assertComposerLockFileFresh($initialState);

        $application = $this->createApplication(new NormalizeCommand(
            new Factory(),
            new ComposerJsonNormalizer(),
            new Formatter(),
            new Differ()
        ));

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

        self::assertContains($expected, $output->fetch());

        $currentState = $scenario->currentState();

        self::assertComposerJsonFileModified($initialState, $currentState);
        self::assertComposerLockFileModified($initialState, $currentState);
        self::assertComposerLockFileFresh($currentState);
    }

    public function testSucceedsWhenComposerJsonIsPresentAndValidAndComposerLockIsPresentAndFreshBeforeAndComposerJsonIsNotYetNormalizedAndComposerLockIsNotFreshAfterAndInformsWhenFileArgumentIsUsed(): void
    {
        $scenario = $this->createScenario(
            CommandInvocation::usingFileArgument(),
            __DIR__ . '/../Fixture/json/valid/lock/present/lock/fresh-before/json/not-yet-normalized/lock/not-fresh-after'
        );

        $initialState = $scenario->initialState();

        self::assertComposerJsonFileExists($initialState);
        self::assertComposerLockFileExists($initialState);
        self::assertComposerLockFileFresh($initialState);

        $application = $this->createApplication(new NormalizeCommand(
            new Factory(),
            new ComposerJsonNormalizer(),
            new Formatter(),
            new Differ()
        ));

        $input = new Console\Input\ArrayInput($scenario->consoleParameters());

        $output = new Console\Output\BufferedOutput();

        $exitCode = $application->run(
            $input,
            $output
        );

        self::assertExitCodeSame(0, $exitCode);

        $renderedOutput = $output->fetch();

        self::assertContains('Note: The file argument is deprecated and will be removed in 2.0.0. Please use the --working-dir option instead.', $renderedOutput);

        $expected = \sprintf(
            'Successfully normalized %s.',
            $scenario->composerJsonFileReference()
        );

        self::assertContains($expected, $renderedOutput);

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
    public function testFailsWhenComposerJsonIsPresentAndValidAndComposerLockIsPresentAndFreshBeforeAndComposerJsonIsNotYetNormalizedAndDryRunOptionIsUsed(CommandInvocation $commandInvocation): void
    {
        $scenario = $this->createScenario(
            $commandInvocation,
            __DIR__ . '/../Fixture/json/valid/lock/present/lock/fresh-before/json/not-yet-normalized/lock/fresh-after'
        );

        $initialState = $scenario->initialState();

        self::assertComposerJsonFileExists($initialState);
        self::assertComposerLockFileExists($initialState);

        $application = $this->createApplication(new NormalizeCommand(
            new Factory(),
            new ComposerJsonNormalizer(),
            new Formatter(),
            new Differ()
        ));

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

        self::assertContains($expected, $renderedOutput);
        self::assertContains('---------- begin diff ----------', $renderedOutput);
        self::assertContains('----------- end diff -----------', $renderedOutput);
        self::assertEquals($initialState, $scenario->currentState());
    }

    /**
     * @dataProvider providerCommandInvocation
     *
     * @param CommandInvocation $commandInvocation
     */
    public function testSucceedsWhenComposerJsonIsPresentAndValidAndComposerLockIsPresentAndFreshBeforeAndComposerJsonIsNotYetNormalizedAndNoUpdateLockOptionIsUsed(CommandInvocation $commandInvocation): void
    {
        $scenario = $this->createScenario(
            $commandInvocation,
            __DIR__ . '/../Fixture/json/valid/lock/present/lock/fresh-before/json/not-yet-normalized/lock/not-fresh-after'
        );

        $initialState = $scenario->initialState();

        self::assertComposerJsonFileExists($initialState);
        self::assertComposerLockFileExists($initialState);
        self::assertComposerLockFileFresh($initialState);

        $application = $this->createApplication(new NormalizeCommand(
            new Factory(),
            new ComposerJsonNormalizer(),
            new Formatter(),
            new Differ()
        ));

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

        self::assertContains($expected, $output->fetch());

        $currentState = $scenario->currentState();

        self::assertComposerJsonFileModified($initialState, $currentState);
        self::assertComposerLockFileNotModified($initialState, $currentState);
        self::assertComposerLockFileNotFresh($currentState);
    }

    public function providerCommandInvocation(): \Generator
    {
        foreach ($this->commandInvocations() as $commandInvocation) {
            yield $commandInvocation->style() => [
                $commandInvocation,
            ];
        }
    }

    public function providerCommandInvocationAndInvalidIndentSize(): \Generator
    {
        $faker = $this->faker();

        $indentSizes = [
            'string-arbitrary' => $faker->sentence,
            'integer-zero-casted-to-string' => (string) 0,
            'integer-less-than-zero-casted-to-string' => (string) (-1 * $faker->numberBetween(1)),
        ];

        foreach ($this->commandInvocations() as $commandInvocation) {
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

    public function providerCommandInvocationIndentSizeAndIndentStyle(): \Generator
    {
        $indentSizes = [
            1,
            $this->faker()->numberBetween(2, 4),
        ];

        $indentStyles = [
            'space',
            'tab',
        ];

        foreach ($this->commandInvocations() as $commandInvocation) {
            foreach ($indentSizes as $indentSize) {
                foreach ($indentStyles as $indentStyle) {
                    $key = \sprintf(
                        '%s-indent-size-%d-indent-style-%s',
                        $commandInvocation->style(),
                        $indentSize,
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

    private function createScenario(CommandInvocation $commandInvocation, string $fixtureDirectory): Scenario
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

    private function createApplication(NormalizeCommand $command): Application
    {
        $application = new Application();

        $application->add($command);
        $application->setAutoExit(false);

        return $application;
    }

    /**
     * @return CommandInvocation[]
     */
    private function commandInvocations(): array
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
            $expected->composerLockFile()->contents(),
            $actual->composerLockFile()->contents(),
            'Failed asserting that initial composer.lock has been modified.'
        );
    }

    private static function assertComposerLockFileNotModified(State $expected, State $actual): void
    {
        self::assertComposerLockFileExists($actual);

        self::assertJsonStringEqualsJsonString(
            $expected->composerLockFile()->contents(),
            $actual->composerLockFile()->contents(),
            'Failed asserting that initial composer.lock has not been modified.'
        );
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
        $projectDirectory = \realpath(__DIR__ . '/../..');

        return \sprintf(
            '%s/test/Fixture',
            $projectDirectory
        );
    }

    private static function temporaryDirectory(): string
    {
        /** @var string $projectDirectory */
        $projectDirectory = \realpath(__DIR__ . '/../..');

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
                '--working-dir' => $state->directory()->path(),
            ]),
            new Console\Output\BufferedOutput()
        );
    }
}
