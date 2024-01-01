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

namespace Ergebnis\Composer\Normalize\Test\Integration\Command\NormalizeCommand\Extra\IsValid\Yes\UsingOptions\Yes;

use Ergebnis\Composer\Normalize\Command;
use Ergebnis\Composer\Normalize\NormalizePlugin;
use Ergebnis\Composer\Normalize\Test;
use Ergebnis\Composer\Normalize\Version;
use PHPUnit\Framework;
use Symfony\Component\Console;

#[Framework\Attributes\CoversClass(Command\NormalizeCommand::class)]
#[Framework\Attributes\CoversClass(NormalizePlugin::class)]
#[Framework\Attributes\UsesClass(Version::class)]
final class CommandInvocationTest extends Test\Integration\Command\NormalizeCommand\AbstractTestCase
{
    #[Framework\Attributes\DataProviderExternal(Test\DataProvider\Command\NormalizeCommandProvider::class, 'commandInvocationIndentSizeAndIndentStyle')]
    public function testSucceeds(
        Test\Util\CommandInvocation $commandInvocation,
        int $indentSize,
        string $indentStyle,
    ): void {
        $scenario = self::createScenario(
            $commandInvocation,
            __DIR__ . '/fixture',
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
            $output,
        );

        $display = $output->fetch();

        self::assertStringContainsString('Configuration provided via options and composer extra. Using configuration from composer extra.', $display);

        $expected = \sprintf(
            'Successfully normalized %s.',
            $scenario->composerJsonFileReference(),
        );

        self::assertStringContainsString($expected, $display);
        self::assertExitCodeSame(0, $exitCode);

        $currentState = $scenario->currentState();

        self::assertComposerJsonFileModified($initialState, $currentState);
        self::assertComposerLockFileNotExists($currentState);
    }
}
