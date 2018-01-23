<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018 Andreas Möller.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/localheinz/composer-normalize
 */

namespace Localheinz\Composer\Normalize\Test\Unit\Command;

use Composer\Command;
use Composer\Composer;
use Composer\Console\Application;
use Composer\Factory;
use Composer\IO;
use Composer\Package;
use Localheinz\Composer\Normalize\Command\NormalizeCommand;
use Localheinz\Json\Normalizer;
use Localheinz\Test\Util\Helper;
use org\bovigo\vfs;
use PHPUnit\Framework;
use Prophecy\Argument;
use Symfony\Component\Console;

final class NormalizeCommandTest extends Framework\TestCase
{
    use Helper;

    /**
     * @var vfs\vfsStreamDirectory
     */
    private $root;

    protected function setUp()
    {
        $this->root = vfs\vfsStream::setup('project');
    }

    protected function tearDown()
    {
        $this->clearComposerFile();
    }

    public function testExtendsBaseCommand(): void
    {
        $this->assertClassExtends(Command\BaseCommand::class, NormalizeCommand::class);
    }

    public function testHasNameAndDescription(): void
    {
        $command = new NormalizeCommand($this->prophesize(Normalizer\NormalizerInterface::class)->reveal());

        $this->assertSame('normalize', $command->getName());
        $this->assertSame('Normalizes composer.json according to its JSON schema (https://getcomposer.org/schema.json).', $command->getDescription());
    }

    public function testHasNoArguments(): void
    {
        $command = new NormalizeCommand($this->prophesize(Normalizer\NormalizerInterface::class)->reveal());

        $definition = $command->getDefinition();

        $this->assertCount(0, $definition->getArguments());
    }

    public function testHasNoUpdateLockOption(): void
    {
        $command = new NormalizeCommand($this->prophesize(Normalizer\NormalizerInterface::class)->reveal());

        $definition = $command->getDefinition();

        $this->assertTrue($definition->hasOption('no-update-lock'));

        $option = $definition->getOption('no-update-lock');

        $this->assertNull($option->getShortcut());
        $this->assertFalse($option->isValueRequired());
        $this->assertFalse($option->getDefault());
        $this->assertSame('Do not update lock file if it exists', $option->getDescription());
    }

    public function testExecuteFailsIfComposerFileDoesNotExist(): void
    {
        $composerFile = $this->pathToNonExistentComposerFile();

        $io = $this->prophesize(IO\ConsoleIO::class);

        $io
            ->writeError(Argument::is(\sprintf(
                '<error>%s not found.</error>',
                $composerFile
            )))
            ->shouldBeCalled();

        $command = new NormalizeCommand($this->prophesize(Normalizer\NormalizerInterface::class)->reveal());

        $command->setIO($io->reveal());

        $tester = new Console\Tester\CommandTester($command);

        $tester->execute([]);

        $this->assertSame(1, $tester->getStatusCode());
        $this->assertFileNotExists($composerFile);
    }

    public function testExecuteFailsIfComposerFileIsNotReadable(): void
    {
        $original = $this->composerFileContent();

        $composerFile = $this->pathToComposerFileWithContent($original);

        \chmod($composerFile, 0222);

        $io = $this->prophesize(IO\ConsoleIO::class);

        $io
            ->writeError(Argument::is(\sprintf(
                '<error>%s is not readable.</error>',
                $composerFile
            )))
            ->shouldBeCalled();

        $command = new NormalizeCommand($this->prophesize(Normalizer\NormalizerInterface::class)->reveal());

        $command->setIO($io->reveal());

        $tester = new Console\Tester\CommandTester($command);

        $tester->execute([]);

        \chmod($composerFile, 0666);

        $this->assertSame(1, $tester->getStatusCode());
        $this->assertFileExists($composerFile);
        $this->assertStringEqualsFile($composerFile, $original);
    }

    public function testExecuteFailsIfComposerFileIsNotWritable(): void
    {
        $original = $this->composerFileContent();

        $composerFile = $this->pathToComposerFileWithContent($original);

        \chmod($composerFile, 0444);

        $io = $this->prophesize(IO\ConsoleIO::class);

        $io
            ->writeError(Argument::is(\sprintf(
                '<error>%s is not writable.</error>',
                $composerFile
            )))
            ->shouldBeCalled();

        $command = new NormalizeCommand($this->prophesize(Normalizer\NormalizerInterface::class)->reveal());

        $command->setIO($io->reveal());

        $tester = new Console\Tester\CommandTester($command);

        $tester->execute([]);

        \chmod($composerFile, 0666);

        $this->assertSame(1, $tester->getStatusCode());
        $this->assertFileExists($composerFile);
        $this->assertStringEqualsFile($composerFile, $original);
    }

    public function testExecuteFailsIfLockerIsLockedButNotFresh(): void
    {
        $original = $this->composerFileContent();

        $composerFile = $this->pathToComposerFileWithContent($original);

        $io = $this->prophesize(IO\ConsoleIO::class);

        $io
            ->writeError(Argument::is('<error>The lock file is not up to date with the latest changes in composer.json, it is recommended that you run `composer update`.</error>'))
            ->shouldBeCalled();

        $locker = $this->prophesize(Package\Locker::class);

        $locker
            ->isLocked()
            ->shouldBeCalled()
            ->willReturn(true);

        $locker
            ->isFresh()
            ->shouldBeCalled()
            ->willReturn(false);

        $composer = $this->prophesize(Composer::class);

        $composer
            ->getLocker()
            ->shouldBeCalled()
            ->willReturn($locker);

        $command = new NormalizeCommand($this->prophesize(Normalizer\NormalizerInterface::class)->reveal());

        $command->setIO($io->reveal());
        $command->setComposer($composer->reveal());

        $tester = new Console\Tester\CommandTester($command);

        $tester->execute([]);

        $this->assertSame(1, $tester->getStatusCode());
        $this->assertFileExists($composerFile);
        $this->assertStringEqualsFile($composerFile, $original);
    }

    public function testExecuteFailsIfNormalizerThrowsInvalidArgumentException(): void
    {
        $faker = $this->faker();

        $exitCode = $faker->numberBetween(2);

        $exception = new \InvalidArgumentException($faker->sentence);

        $original = $this->composerFileContent();

        $composerFile = $this->pathToComposerFileWithContent($original);

        $io = $this->prophesize(IO\ConsoleIO::class);

        $io
            ->writeError(Argument::is(\sprintf(
                '<error>%s</error>',
                $exception->getMessage()
            )))
            ->shouldBeCalled();

        $locker = $this->prophesize(Package\Locker::class);

        $locker
            ->isLocked()
            ->shouldBeCalled()
            ->willReturn(true);

        $locker
            ->isFresh()
            ->shouldBeCalled()
            ->willReturn(true);

        $composer = $this->prophesize(Composer::class);

        $composer
            ->getLocker()
            ->shouldBeCalled()
            ->willReturn($locker);

        $application = $this->prophesize(Application::class);

        $application
            ->getHelperSet()
            ->shouldBeCalled()
            ->willReturn(new Console\Helper\HelperSet());

        $application
            ->getDefinition()
            ->shouldBeCalled()
            ->willReturn($this->createDefinitionMock());

        $application
            ->run(
                Argument::allOf(
                    Argument::type(Console\Input\StringInput::class),
                    Argument::that(function (Console\Input\StringInput $input) {
                        return 'validate --no-check-all --no-check-lock --no-check-publish --strict' === (string) $input;
                    })
                ),
                Argument::type(Console\Output\OutputInterface::class)
            )
            ->shouldBeCalled()
            ->willReturn($exitCode);

        $normalizer = $this->prophesize(Normalizer\NormalizerInterface::class);

        $normalizer
            ->normalize(Argument::is($original))
            ->shouldBeCalled()
            ->willThrow($exception);

        $command = new NormalizeCommand($normalizer->reveal());

        $command->setIO($io->reveal());
        $command->setComposer($composer->reveal());
        $command->setApplication($application->reveal());

        $tester = new Console\Tester\CommandTester($command);

        $tester->execute([]);

        $this->assertSame($exitCode, $tester->getStatusCode());
        $this->assertFileExists($composerFile);
        $this->assertStringEqualsFile($composerFile, $original);
    }

    public function testExecuteFailsIfNormalizerThrowsRuntimeException(): void
    {
        $exception = new \RuntimeException($this->faker()->sentence);

        $original = $this->composerFileContent();

        $composerFile = $this->pathToComposerFileWithContent($original);

        $io = $this->prophesize(IO\ConsoleIO::class);

        $io
            ->writeError(Argument::is(\sprintf(
                '<error>%s</error>',
                $exception->getMessage()
            )))
            ->shouldBeCalled();

        $locker = $this->prophesize(Package\Locker::class);

        $locker
            ->isLocked()
            ->shouldBeCalled()
            ->willReturn(true);

        $locker
            ->isFresh()
            ->shouldBeCalled()
            ->willReturn(true);

        $composer = $this->prophesize(Composer::class);

        $composer
            ->getLocker()
            ->shouldBeCalled()
            ->willReturn($locker);

        $normalizer = $this->prophesize(Normalizer\NormalizerInterface::class);

        $normalizer
            ->normalize(Argument::is($original))
            ->shouldBeCalled()
            ->willThrow($exception);

        $command = new NormalizeCommand($normalizer->reveal());

        $command->setIO($io->reveal());
        $command->setComposer($composer->reveal());

        $tester = new Console\Tester\CommandTester($command);

        $tester->execute([]);

        $this->assertSame(1, $tester->getStatusCode());
        $this->assertFileExists($composerFile);
        $this->assertStringEqualsFile($composerFile, $original);
    }

    public function testExecuteSucceedsIfLockerIsNotLockedAndComposerFileIsAlreadyNormalized(): void
    {
        $original = $this->composerFileContent();

        $composerFile = $this->pathToComposerFileWithContent($original);

        $io = $this->prophesize(IO\ConsoleIO::class);

        $io
            ->write(Argument::is(\sprintf(
                '<info>%s is already normalized.</info>',
                $composerFile
            )))
            ->shouldBeCalled();

        $locker = $this->prophesize(Package\Locker::class);

        $locker
            ->isLocked()
            ->shouldBeCalled()
            ->willReturn(false);

        $composer = $this->prophesize(Composer::class);

        $composer
            ->getLocker()
            ->shouldBeCalled()
            ->willReturn($locker);

        $normalizer = $this->prophesize(Normalizer\NormalizerInterface::class);

        $normalizer
            ->normalize(Argument::is($original))
            ->shouldBeCalled()
            ->willReturn($original);

        $command = new NormalizeCommand($normalizer->reveal());

        $command->setIO($io->reveal());
        $command->setComposer($composer->reveal());

        $tester = new Console\Tester\CommandTester($command);

        $tester->execute([]);

        $this->assertSame(0, $tester->getStatusCode());
        $this->assertFileExists($composerFile);
        $this->assertStringEqualsFile($composerFile, $original);
    }

    public function testExecuteSucceedsIfLockerIsLockedAndFreshButComposerFileIsAlreadyNormalized(): void
    {
        $original = $this->composerFileContent();

        $composerFile = $this->pathToComposerFileWithContent($original);

        $io = $this->prophesize(IO\ConsoleIO::class);

        $io
            ->write(Argument::is(\sprintf(
                '<info>%s is already normalized.</info>',
                $composerFile
            )))
            ->shouldBeCalled();

        $locker = $this->prophesize(Package\Locker::class);

        $locker
            ->isLocked()
            ->shouldBeCalled()
            ->willReturn(true);

        $locker
            ->isFresh()
            ->shouldBeCalled()
            ->willReturn(true);

        $composer = $this->prophesize(Composer::class);

        $composer
            ->getLocker()
            ->shouldBeCalled()
            ->willReturn($locker);

        $normalizer = $this->prophesize(Normalizer\NormalizerInterface::class);

        $normalizer
            ->normalize(Argument::is($original))
            ->shouldBeCalled()
            ->willReturn($original);

        $command = new NormalizeCommand($normalizer->reveal());

        $command->setIO($io->reveal());
        $command->setComposer($composer->reveal());

        $tester = new Console\Tester\CommandTester($command);

        $tester->execute([]);

        $this->assertSame(0, $tester->getStatusCode());
        $this->assertFileExists($composerFile);
        $this->assertStringEqualsFile($composerFile, $original);
    }

    public function testExecuteSucceedsIfLockerIsNotLockedAndComposerFileWasNormalizedSuccessfully(): void
    {
        $original = $this->composerFileContent();

        $normalized = \json_encode(\array_reverse(\json_decode(
            $original,
            true
        )));

        $composerFile = $this->pathToComposerFileWithContent($original);

        $io = $this->prophesize(IO\ConsoleIO::class);

        $io
            ->write(Argument::is(\sprintf(
                '<info>Successfully normalized %s.</info>',
                $composerFile
            )))
            ->shouldBeCalled();

        $locker = $this->prophesize(Package\Locker::class);

        $locker
            ->isLocked()
            ->shouldBeCalled()
            ->willReturn(false);

        $composer = $this->prophesize(Composer::class);

        $composer
            ->getLocker()
            ->shouldBeCalled()
            ->willReturn($locker);

        $normalizer = $this->prophesize(Normalizer\NormalizerInterface::class);

        $normalizer
            ->normalize(Argument::is($original))
            ->shouldBeCalled()
            ->willReturn($normalized);

        $command = new NormalizeCommand($normalizer->reveal());

        $command->setIO($io->reveal());
        $command->setComposer($composer->reveal());

        $tester = new Console\Tester\CommandTester($command);

        $tester->execute([]);

        $this->assertSame(0, $tester->getStatusCode());
        $this->assertFileExists($composerFile);
        $this->assertStringEqualsFile($composerFile, $normalized);
    }

    public function testExecuteSucceedsIfLockerIsLockedAndLockerCouldBeUpdatedAfterNormalization(): void
    {
        $original = $this->composerFileContent();

        $normalized = \json_encode(\array_reverse(\json_decode(
            $original,
            true
        )));

        $composerFile = $this->pathToComposerFileWithContent($original);

        $io = $this->prophesize(IO\ConsoleIO::class);

        $io
            ->write(Argument::is(\sprintf(
                '<info>Successfully normalized %s.</info>',
                $composerFile
            )))
            ->shouldBeCalled();

        $io
            ->write(Argument::is('<info>Updating lock file.</info>'))
            ->shouldBeCalled();

        $locker = $this->prophesize(Package\Locker::class);

        $locker
            ->isLocked()
            ->shouldBeCalled()
            ->willReturn(true);

        $locker
            ->isFresh()
            ->shouldBeCalled()
            ->willReturn(true);

        $composer = $this->prophesize(Composer::class);

        $composer
            ->getLocker()
            ->shouldBeCalled()
            ->willReturn($locker);

        $application = $this->prophesize(Application::class);

        $application
            ->getHelperSet()
            ->shouldBeCalled()
            ->willReturn(new Console\Helper\HelperSet());

        $application
            ->getDefinition()
            ->shouldBeCalled()
            ->willReturn($this->createDefinitionMock());

        $application
            ->run(
                Argument::allOf(
                    Argument::type(Console\Input\StringInput::class),
                    Argument::that(function (Console\Input\StringInput $input) {
                        return 'update --lock --no-autoloader --no-plugins --no-scripts --no-suggest' === (string) $input;
                    })
                ),
                Argument::type(Console\Output\OutputInterface::class)
            )
            ->shouldBeCalled()
            ->willReturn(0);

        $normalizer = $this->prophesize(Normalizer\NormalizerInterface::class);

        $normalizer
            ->normalize(Argument::is($original))
            ->shouldBeCalled()
            ->willReturn($normalized);

        $command = new NormalizeCommand($normalizer->reveal());

        $command->setIO($io->reveal());
        $command->setComposer($composer->reveal());
        $command->setApplication($application->reveal());

        $tester = new Console\Tester\CommandTester($command);

        $tester->execute([]);

        $this->assertSame(0, $tester->getStatusCode());
        $this->assertFileExists($composerFile);
        $this->assertStringEqualsFile($composerFile, $normalized);
    }

    public function testExecuteSucceedsIfLockerIsLockedButSkipsUpdatingLockerIfNoUpdateLockOptionIsUsed(): void
    {
        $original = $this->composerFileContent();

        $normalized = \json_encode(\array_reverse(\json_decode(
            $original,
            true
        )));

        $composerFile = $this->pathToComposerFileWithContent($original);

        $io = $this->prophesize(IO\ConsoleIO::class);

        $io
            ->write(Argument::is(\sprintf(
                '<info>Successfully normalized %s.</info>',
                $composerFile
            )))
            ->shouldBeCalled();

        $locker = $this->prophesize(Package\Locker::class);

        $locker
            ->isLocked()
            ->shouldBeCalled()
            ->willReturn(true);

        $locker
            ->isFresh()
            ->shouldBeCalled()
            ->willReturn(true);

        $composer = $this->prophesize(Composer::class);

        $composer
            ->getLocker()
            ->shouldBeCalled()
            ->willReturn($locker);

        $application = $this->prophesize(Application::class);

        $application
            ->getHelperSet()
            ->shouldBeCalled()
            ->willReturn(new Console\Helper\HelperSet());

        $application
            ->getDefinition()
            ->shouldBeCalled()
            ->willReturn($this->createDefinitionMock());

        $application
            ->run()
            ->shouldNotBeCalled();

        $normalizer = $this->prophesize(Normalizer\NormalizerInterface::class);

        $normalizer
            ->normalize(Argument::is($original))
            ->shouldBeCalled()
            ->willReturn($normalized);

        $command = new NormalizeCommand($normalizer->reveal());

        $command->setIO($io->reveal());
        $command->setComposer($composer->reveal());
        $command->setApplication($application->reveal());

        $tester = new Console\Tester\CommandTester($command);

        $tester->execute([
            '--no-update-lock' => null,
        ]);

        $this->assertSame(0, $tester->getStatusCode());
        $this->assertFileExists($composerFile);
        $this->assertStringEqualsFile($composerFile, $normalized);
    }

    private function composerFileContent(): string
    {
        static $content;

        if (null === $content) {
            $content = \file_get_contents(__DIR__ . '/../../../composer.json');
        }

        return $content;
    }

    /**
     * Creates a composer.json with the specified content and returns the path to it.
     *
     * @param string $content
     *
     * @return string
     */
    private function pathToComposerFileWithContent(string $content): string
    {
        $composerFile = $this->pathToComposerFile();

        \file_put_contents($composerFile, $content);

        $this->useComposerFile($composerFile);

        return $composerFile;
    }

    /**
     * Returns the path to a non-existent composer.json.
     *
     * @return string
     */
    private function pathToNonExistentComposerFile(): string
    {
        $composerFile = $this->pathToComposerFile();

        $this->useComposerFile($composerFile);

        return $composerFile;
    }

    /**
     * Returns the path to a composer.json (which may not exist).
     *
     * @return string
     */
    private function pathToComposerFile(): string
    {
        return $this->root->url() . '/composer.json';
    }

    /**
     * @see Factory::getComposerFile()
     *
     * @param string $composerFile
     */
    private function useComposerFile(string $composerFile)
    {
        \putenv(\sprintf(
            'COMPOSER=%s',
            $composerFile
        ));
    }

    /**
     * @see Factory::getComposerFile()
     */
    private function clearComposerFile(): void
    {
        \putenv('COMPOSER');
    }

    /**
     * @see Console\Tester\CommandTester::execute()
     *
     * @return Console\Input\InputDefinition
     */
    private function createDefinitionMock(): Console\Input\InputDefinition
    {
        $definition = $this->prophesize(Console\Input\InputDefinition::class);

        $definition
            ->hasArgument('command')
            ->shouldBeCalled()
            ->willReturn(false);

        $definition
            ->getArguments()
            ->shouldBeCalled()
            ->willReturn([]);

        $definition
            ->getOptions()
            ->shouldBeCalled()
            ->willReturn([]);

        return $definition->reveal();
    }
}
