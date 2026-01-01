<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018-2026 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/composer-normalize
 */

namespace Ergebnis\Composer\Normalize\Test\Integration\Command\NormalizeCommand\Normalizer\Throws;

use Composer\Factory;
use Ergebnis\Composer\Normalize\Command;
use Ergebnis\Composer\Normalize\Test;
use Ergebnis\Json\Json;
use Ergebnis\Json\Normalizer;
use Ergebnis\Json\Printer;
use Localheinz\Diff;
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
    public function testFailsWhenNormalizerThrowsRuntimeExceptionDuringNormalization(Test\Util\CommandInvocation $commandInvocation): void
    {
        $scenario = self::createScenario(
            $commandInvocation,
            __DIR__ . '/fixture',
        );

        $initialState = $scenario->initialState();

        self::assertComposerJsonFileExists($initialState);
        self::assertComposerLockFileNotExists($initialState);

        $exceptionMessage = self::faker()->sentence();

        $application = self::createApplication(new Command\NormalizeCommand(
            new Factory(),
            new class($exceptionMessage) implements Normalizer\Normalizer {
                private string $exceptionMessage;

                public function __construct(string $exceptionMessage)
                {
                    $this->exceptionMessage = $exceptionMessage;
                }

                public function normalize(Json $json): Json
                {
                    throw new \RuntimeException($this->exceptionMessage);
                }
            },
            new Printer\Printer(),
            new Diff\Differ(new Diff\Output\StrictUnifiedDiffOutputBuilder([
                'fromFile' => 'original',
                'toFile' => 'normalized',
            ])),
        ));

        $input = new Console\Input\ArrayInput($scenario->consoleParameters());

        $output = new Console\Output\BufferedOutput();

        $exitCode = $application->run(
            $input,
            $output,
        );

        self::assertStringContainsString($exceptionMessage, $output->fetch());
        self::assertExitCodeSame(1, $exitCode);
        self::assertEquals($initialState, $scenario->currentState());
    }
}
