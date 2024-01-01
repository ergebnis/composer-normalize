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

namespace Ergebnis\Composer\Normalize\Test\Integration\Command\NormalizeCommand\Extra\IsValid\No\IndentStyle\NotSpaceOrTab;

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
    #[Framework\Attributes\DataProviderExternal(Test\DataProvider\Command\NormalizeCommandProvider::class, 'commandInvocation')]
    public function testFails(Test\Util\CommandInvocation $commandInvocation): void
    {
        $scenario = self::createScenario(
            $commandInvocation,
            __DIR__ . '/fixture',
        );

        $application = self::createApplicationWithNormalizeCommandAsProvidedByNormalizePlugin();

        $input = new Console\Input\ArrayInput($scenario->consoleParameters());

        $output = new Console\Output\BufferedOutput();

        $exitCode = $application->run(
            $input,
            $output,
        );

        self::assertStringContainsString('Indent style needs to be one of "space", "tab", but "foo" is not.', $output->fetch());
        self::assertExitCodeSame(1, $exitCode);
        self::assertEquals($scenario->initialState(), $scenario->currentState());
    }
}
