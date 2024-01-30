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

namespace Command\NormalizeCommand\Json\IsValid\Yes\Lock\IsPresent\Yes\IsFreshBefore\Yes\Json\IsNormalized\Yes;

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

        $expected = \sprintf(
            '%s is already normalized.',
            $scenario->composerJsonFileReference(),
        );

        self::assertStringContainsString($expected, $output->fetch());
        self::assertExitCodeSame(0, $exitCode);
        self::assertEquals($initialState, $scenario->currentState());
    }
}
