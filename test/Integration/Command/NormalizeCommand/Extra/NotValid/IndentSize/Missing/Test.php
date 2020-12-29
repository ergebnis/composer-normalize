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

namespace Ergebnis\Composer\Normalize\Test\Integration\Command\NormalizeCommand\Extra\NotValid\IndentSize\Missing;

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
    public function testFails(Util\CommandInvocation $commandInvocation): void
    {
        $scenario = self::createScenario(
            $commandInvocation,
            __DIR__ . '/fixture'
        );

        $application = self::createApplicationWithNormalizeCommandAsProvidedByNormalizePlugin();

        $input = new Console\Input\ArrayInput($scenario->consoleParameters());

        $output = new Console\Output\BufferedOutput();

        $exitCode = $application->run(
            $input,
            $output
        );

        self::assertExitCodeSame(1, $exitCode);
        self::assertStringContainsString('Configuration in composer extra requires keys "indent-size", "indent-style" with corresponding values.', $output->fetch());
        self::assertEquals($scenario->initialState(), $scenario->currentState());
    }
}
