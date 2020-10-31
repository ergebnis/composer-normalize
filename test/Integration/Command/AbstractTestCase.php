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
use Ergebnis\Composer\Normalize\Command\NormalizeCommand;
use Ergebnis\Composer\Normalize\NormalizePlugin;
use Ergebnis\Composer\Normalize\Test;
use Ergebnis\Test\Util;
use PHPUnit\Framework;
use Symfony\Component\Console;
use Symfony\Component\Filesystem;

/**
 * @internal
 *
 * @coversNothing
 */
abstract class AbstractTestCase extends Framework\TestCase
{
    use Util\Helper;

    /**
     * @var string
     */
    private $currentWorkingDirectory;

    final protected function setUp(): void
    {
        self::fileSystem()->remove(self::temporaryDirectory());

        $currentWorkingDirectory = \getcwd();

        if (false === $currentWorkingDirectory) {
            throw new \RuntimeException('Unable to determine current working directory.');
        }

        $this->currentWorkingDirectory = $currentWorkingDirectory;
    }

    final protected function tearDown(): void
    {
        self::fileSystem()->remove(self::temporaryDirectory());

        \chdir($this->currentWorkingDirectory);
    }

    final protected static function createScenario(
        Test\Util\CommandInvocation $commandInvocation,
        string $fixtureDirectory
    ): Test\Util\Scenario {
        if (!\is_dir($fixtureDirectory)) {
            throw new \InvalidArgumentException(\sprintf(
                'Fixture directory "%s" does not exist',
                $fixtureDirectory
            ));
        }

        $fileSystem = self::fileSystem();

        $fileSystem->remove(self::temporaryDirectory());

        $fileSystem->mirror(
            $fixtureDirectory,
            self::temporaryDirectory()
        );

        $scenario = Test\Util\Scenario::fromCommandInvocationAndInitialState(
            $commandInvocation,
            Test\Util\State::fromDirectory(Test\Util\Directory::fromPath(self::temporaryDirectory()))
        );

        if ($commandInvocation->is(Test\Util\CommandInvocation::inCurrentWorkingDirectory())) {
            \chdir($scenario->directory()->path());
        }

        return $scenario;
    }

    final protected static function createApplication(NormalizeCommand $command): Application
    {
        $application = new Application();

        $application->add($command);
        $application->setAutoExit(false);

        return $application;
    }

    final protected static function createApplicationWithNormalizeCommandAsProvidedByNormalizePlugin(): Application
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

    final protected static function assertComposerJsonFileExists(Test\Util\State $state): void
    {
        self::assertFileExists($state->composerJsonFile()->path());
    }

    final protected static function assertComposerJsonFileModified(
        Test\Util\State $expected,
        Test\Util\State $actual
    ): void {
        self::assertComposerJsonFileExists($actual);

        self::assertNotEquals(
            $expected->composerJsonFile()->contents(),
            $actual->composerJsonFile()->contents(),
            'Failed asserting that initial composer.json has been modified.'
        );
    }

    final protected static function assertComposerLockFileExists(Test\Util\State $state): void
    {
        self::assertFileExists($state->composerLockFile()->path());
    }

    final protected static function assertComposerLockFileNotExists(Test\Util\State $state): void
    {
        self::assertFileNotExists($state->composerLockFile()->path());
    }

    final protected static function assertComposerLockFileFresh(Test\Util\State $state): void
    {
        self::assertComposerJsonFileExists($state);
        self::assertComposerLockFileExists($state);

        $exitCode = self::validateComposer($state);

        self::assertSame(0, $exitCode, \sprintf(
            'Failed asserting that composer.lock is fresh in %s.',
            $state->directory()->path()
        ));
    }

    final protected static function assertComposerLockFileNotFresh(Test\Util\State $state): void
    {
        self::assertComposerJsonFileExists($state);
        self::assertComposerLockFileExists($state);

        $exitCode = self::validateComposer($state);

        self::assertNotSame(0, $exitCode, \sprintf(
            'Failed asserting that composer.lock is not fresh in %s.',
            $state->directory()->path()
        ));
    }

    final protected static function assertComposerLockFileModified(Test\Util\State $expected, Test\Util\State $actual): void
    {
        self::assertComposerLockFileExists($actual);

        self::assertJsonStringNotEqualsJsonString(
            self::normalizeLockFileContents($expected->composerLockFile()->contents()),
            self::normalizeLockFileContents($actual->composerLockFile()->contents()),
            'Failed asserting that initial composer.lock has been modified.'
        );
    }

    final protected static function assertComposerLockFileNotModified(Test\Util\State $expected, Test\Util\State $actual): void
    {
        self::assertComposerLockFileExists($actual);

        self::assertJsonStringEqualsJsonString(
            self::normalizeLockFileContents($expected->composerLockFile()->contents()),
            self::normalizeLockFileContents($actual->composerLockFile()->contents()),
            'Failed asserting that initial composer.lock has not been modified.'
        );
    }

    final protected static function assertExitCodeSame(int $expected, int $actual): void
    {
        self::assertSame($expected, $actual, \sprintf(
            'Failed asserting that exit code %d is identical to %d.',
            $actual,
            $expected
        ));
    }

    private static function fileSystem(): Filesystem\Filesystem
    {
        return new Filesystem\Filesystem();
    }

    private static function temporaryDirectory(): string
    {
        return __DIR__ . '/../../../.build/test';
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

    private static function validateComposer(Test\Util\State $state): int
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
