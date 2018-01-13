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
use Prophecy\Prophecy;
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

    public function testExtendsBaseCommand()
    {
        $this->assertClassExtends(Command\BaseCommand::class, NormalizeCommand::class);
    }

    public function testHasNameAndDescription()
    {
        $command = new NormalizeCommand($this->prophesize(Normalizer\NormalizerInterface::class)->reveal());

        $this->assertSame('normalize', $command->getName());
        $this->assertSame('Normalizes composer.json according to its JSON schema (https://getcomposer.org/schema.json).', $command->getDescription());
    }

    public function testHasNoArguments()
    {
        $command = new NormalizeCommand($this->prophesize(Normalizer\NormalizerInterface::class)->reveal());

        $definition = $command->getDefinition();

        $this->assertCount(0, $definition->getArguments());
    }

    public function testHasNoOptions()
    {
        $command = new NormalizeCommand($this->prophesize(Normalizer\NormalizerInterface::class)->reveal());

        $definition = $command->getDefinition();

        $this->assertCount(0, $definition->getOptions());
    }

    public function testExecuteFailsIfComposerFileDoesNotExist()
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

    public function testExecuteFailsIfComposerFileIsNotReadable()
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

    public function testExecuteFailsIfComposerFileIsNotWritable()
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

    public function testExecuteFailsIfLockerIsLockedButNotFresh()
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

    /**
     * @dataProvider providerNormalizerException
     *
     * @param \Exception $exception
     */
    public function testExecuteFailsIfNormalizerThrowsException(\Exception $exception)
    {
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

    public function providerNormalizerException(): \Generator
    {
        $classNames = [
            \InvalidArgumentException::class,
            \RuntimeException::class,
        ];

        foreach ($classNames as $className) {
            yield $className => [
                new $className($this->faker()->sentence),
            ];
        }
    }

    public function testExecuteSucceedsIfLockerIsNotLockedAndComposerFileIsAlreadyNormalized()
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

    public function testExecuteSucceedsIfLockerIsLockedAndFreshButComposerFileIsAlreadyNormalized()
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

    public function testExecuteSucceedsIfLockerIsNotLockedAndComposerFileWasNormalizedSuccessfully()
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

    public function testExecuteFailsIfLockerIsLockedAndFreshButLockerCouldNotBeUpdatedAfterNormalization()
    {
        $original = $this->composerFileContent();

        $normalized = \json_encode(\array_reverse(\json_decode(
            $original,
            true
        )));

        $composerFile = $this->pathToComposerFileWithContent($original);

        $io = $this->prophesize(IO\ConsoleIO::class);

        $io
            ->writeError(Argument::is(\sprintf(
                '<error>Successfully normalized %s, but could not update lock file.</error>',
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
            ->willReturn($this->createDefinitionProphecy()->reveal());

        $application
            ->run(
                Argument::allOf(
                    Argument::type(Console\Input\StringInput::class),
                    Argument::that(function (Console\Input\StringInput $input) {
                        return 'update --lock --no-plugins' === (string) $input;
                    })
                ),
                Argument::type(Console\Output\NullOutput::class)
            )
            ->shouldBeCalled()
            ->willReturn(1);

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

        $this->assertSame(1, $tester->getStatusCode());
        $this->assertFileExists($composerFile);
        $this->assertStringEqualsFile($composerFile, $normalized);
    }

    public function testExecuteSucceedsIfLockerIsLockedAndLockerCouldBeUpdatedAfterNormalization()
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
            ->willReturn($this->createDefinitionProphecy());

        $application
            ->run(
                Argument::allOf(
                    Argument::type(Console\Input\StringInput::class),
                    Argument::that(function (Console\Input\StringInput $input) {
                        return 'update --lock --no-plugins' === (string) $input;
                    })
                ),
                Argument::type(Console\Output\NullOutput::class)
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
    private function clearComposerFile()
    {
        \putenv('COMPOSER');
    }

    /**
     * @see Console\Tester\CommandTester::execute()
     *
     * @return Prophecy\ObjectProphecy
     */
    private function createDefinitionProphecy(): Prophecy\ObjectProphecy
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

        return $definition;
    }
}
