<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018-2023 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/composer-normalize
 */

namespace Ergebnis\Composer\Normalize\Test\Integration\Command\NormalizeCommand\Options\NotValid;

use Ergebnis\Composer\Normalize\Test\Integration;
use Ergebnis\Composer\Normalize\Test\Util;
use Ergebnis\Json\Normalizer;
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
    public function testFailsWhenIndentStyleOptionIsUsedWithoutIndentSizeOption(Util\CommandInvocation $commandInvocation): void
    {
        /** @var string $indentStyle */
        $indentStyle = self::faker()->randomElement(\array_keys(Normalizer\Format\Indent::CHARACTERS));

        $scenario = self::createScenario(
            $commandInvocation,
            __DIR__ . '/fixture',
        );

        $application = self::createApplicationWithNormalizeCommandAsProvidedByNormalizePlugin();

        $input = new Console\Input\ArrayInput($scenario->consoleParametersWith([
            '--indent-style' => $indentStyle,
        ]));

        $output = new Console\Output\BufferedOutput();

        $exitCode = $application->run(
            $input,
            $output,
        );

        self::assertExitCodeSame(1, $exitCode);
        self::assertStringContainsString('When using the indent-style option, an indent size needs to be specified using the indent-size option.', $output->fetch());
        self::assertEquals($scenario->initialState(), $scenario->currentState());
    }

    /**
     * @dataProvider \Ergebnis\Composer\Normalize\Test\DataProvider\Command\NormalizeCommandProvider::commandInvocation()
     */
    public function testFailsWhenIndentSizeOptionIsUsedWithoutIndentStyleOption(Util\CommandInvocation $commandInvocation): void
    {
        $scenario = self::createScenario(
            $commandInvocation,
            __DIR__ . '/fixture',
        );

        $application = self::createApplicationWithNormalizeCommandAsProvidedByNormalizePlugin();

        $input = new Console\Input\ArrayInput($scenario->consoleParametersWith([
            '--indent-size' => (string) self::faker()->numberBetween(1, 4),
        ]));

        $output = new Console\Output\BufferedOutput();

        $exitCode = $application->run(
            $input,
            $output,
        );

        self::assertExitCodeSame(1, $exitCode);
        self::assertStringContainsString('When using the indent-size option, an indent style (one of "space", "tab") needs to be specified using the indent-style option.', $output->fetch());
        self::assertEquals($scenario->initialState(), $scenario->currentState());
    }

    /**
     * @dataProvider \Ergebnis\Composer\Normalize\Test\DataProvider\Command\NormalizeCommandProvider::commandInvocation()
     */
    public function testFailsWhenIndentStyleOptionIsInvalid(Util\CommandInvocation $commandInvocation): void
    {
        $faker = self::faker();

        $indentStyle = $faker->sentence;

        $scenario = self::createScenario(
            $commandInvocation,
            __DIR__ . '/fixture',
        );

        $application = self::createApplicationWithNormalizeCommandAsProvidedByNormalizePlugin();

        $input = new Console\Input\ArrayInput($scenario->consoleParametersWith([
            '--indent-size' => (string) $faker->numberBetween(1, 4),
            '--indent-style' => $indentStyle,
        ]));

        $output = new Console\Output\BufferedOutput();

        $exitCode = $application->run(
            $input,
            $output,
        );

        self::assertExitCodeSame(1, $exitCode);

        $expected = \sprintf(
            'Indent style needs to be one of "space", "tab", but "%s" is not.',
            $indentStyle,
        );

        self::assertStringContainsString($expected, $output->fetch());
        self::assertEquals($scenario->initialState(), $scenario->currentState());
    }

    /**
     * @dataProvider \Ergebnis\Composer\Normalize\Test\DataProvider\Command\NormalizeCommandProvider::commandInvocationAndInvalidIndentSize
     */
    public function testFailsWhenIndentSizeOptionIsInvalid(
        Util\CommandInvocation $commandInvocation,
        string $indentSize,
    ): void {
        /** @var string $indentStyle */
        $indentStyle = self::faker()->randomElement(\array_keys(Normalizer\Format\Indent::CHARACTERS));

        $scenario = self::createScenario(
            $commandInvocation,
            __DIR__ . '/fixture',
        );

        $application = self::createApplicationWithNormalizeCommandAsProvidedByNormalizePlugin();

        $input = new Console\Input\ArrayInput($scenario->consoleParametersWith([
            '--indent-size' => $indentSize,
            '--indent-style' => $indentStyle,
        ]));

        $output = new Console\Output\BufferedOutput();

        $exitCode = $application->run(
            $input,
            $output,
        );

        self::assertExitCodeSame(1, $exitCode);

        $expected = \sprintf(
            'Indent size needs to be an integer greater than 0, but "%s" is not.',
            $indentSize,
        );

        self::assertStringContainsString($expected, $output->fetch());
        self::assertEquals($scenario->initialState(), $scenario->currentState());
    }
}
