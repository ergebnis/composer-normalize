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

/**
 * @internal
 */
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
        $command = new NormalizeCommand(
            $this->prophesize(Factory::class)->reveal(),
            $this->prophesize(Normalizer\NormalizerInterface::class)->reveal()
        );

        $this->assertSame('normalize', $command->getName());
        $this->assertSame('Normalizes composer.json according to its JSON schema (https://getcomposer.org/schema.json).', $command->getDescription());
    }

    public function testHasFileArgument(): void
    {
        $command = new NormalizeCommand(
            $this->prophesize(Factory::class)->reveal(),
            $this->prophesize(Normalizer\NormalizerInterface::class)->reveal()
        );

        $definition = $command->getDefinition();

        $this->assertTrue($definition->hasArgument('file'));

        $argument = $definition->getArgument('file');

        $this->assertFalse($argument->isRequired());
        $this->assertSame('Path to composer.json file', $argument->getDescription());
        $this->assertNull($argument->getDefault());
    }

    public function testHasDryRunOption(): void
    {
        $command = new NormalizeCommand(
            $this->prophesize(Factory::class)->reveal(),
            $this->prophesize(Normalizer\NormalizerInterface::class)->reveal()
        );

        $definition = $command->getDefinition();

        $this->assertTrue($definition->hasOption('dry-run'));

        $option = $definition->getOption('dry-run');

        $this->assertNull($option->getShortcut());
        $this->assertFalse($option->isValueRequired());
        $this->assertFalse($option->getDefault());
        $this->assertSame('Show the results of normalizing, but do not modify any files', $option->getDescription());
    }

    public function testHasIndentSizeOption(): void
    {
        $command = new NormalizeCommand(
            $this->prophesize(Factory::class)->reveal(),
            $this->prophesize(Normalizer\NormalizerInterface::class)->reveal()
        );

        $definition = $command->getDefinition();

        $this->assertTrue($definition->hasOption('indent-size'));

        $option = $definition->getOption('indent-size');

        $this->assertNull($option->getShortcut());
        $this->assertTrue($option->isValueRequired());
        $this->assertNull($option->getDefault());
        $this->assertSame('Indent size (an integer greater than 0); should be used with the --indent-style option', $option->getDescription());
    }

    public function testHasIndentStyleOption(): void
    {
        $command = new NormalizeCommand(
            $this->prophesize(Factory::class)->reveal(),
            $this->prophesize(Normalizer\NormalizerInterface::class)->reveal()
        );

        $definition = $command->getDefinition();

        $this->assertTrue($definition->hasOption('indent-style'));

        $option = $definition->getOption('indent-style');

        $this->assertNull($option->getShortcut());
        $this->assertTrue($option->isValueRequired());
        $this->assertNull($option->getDefault());

        $description = \sprintf(
            'Indent style (one of "%s"); should be used with the --indent-size option',
            \implode('", "', \array_keys($this->indentStyles()))
        );

        $this->assertSame($description, $option->getDescription());
    }

    public function testHasNoUpdateLockOption(): void
    {
        $command = new NormalizeCommand(
            $this->prophesize(Factory::class)->reveal(),
            $this->prophesize(Normalizer\NormalizerInterface::class)->reveal()
        );

        $definition = $command->getDefinition();

        $this->assertTrue($definition->hasOption('no-update-lock'));

        $option = $definition->getOption('no-update-lock');

        $this->assertNull($option->getShortcut());
        $this->assertFalse($option->isValueRequired());
        $this->assertFalse($option->getDefault());
        $this->assertSame('Do not update lock file if it exists', $option->getDescription());
    }

    public function testExecuteWithIndentFailsIfIndentStyleOptionIsNotUsed(): void
    {
        $original = $this->composerFileContent();

        $composerFile = $this->pathToComposerFileWithContent($original);

        $io = $this->prophesize(IO\ConsoleIO::class);

        $io
            ->writeError(Argument::is(\sprintf(
                '<error>When using the indent-size option, an indent style (one of "%s") needs to be specified using the indent-style option.</error>',
                \implode('", "', \array_keys($this->indentStyles()))
            )))
            ->shouldBeCalled();

        $command = new NormalizeCommand(
            $this->prophesize(Factory::class)->reveal(),
            $this->prophesize(Normalizer\NormalizerInterface::class)->reveal()
        );

        $command->setIO($io->reveal());

        $tester = new Console\Tester\CommandTester($command);

        $tester->execute([
            'file' => $composerFile,
            '--indent-size' => $this->faker()->numberBetween(1),
        ]);

        $this->assertSame(1, $tester->getStatusCode());
        $this->assertFileExists($composerFile);
        $this->assertStringEqualsFile($composerFile, $original);
    }

    public function testExecuteWithIndentFailsIfIndentSizeOptionIsNotUsed(): void
    {
        $original = $this->composerFileContent();

        $composerFile = $this->pathToComposerFileWithContent($original);

        $io = $this->prophesize(IO\ConsoleIO::class);

        $io
            ->writeError(Argument::is('<error>When using the indent-style option, an indent size needs to be specified using the indent-size option.</error>'))
            ->shouldBeCalled();

        $command = new NormalizeCommand(
            $this->prophesize(Factory::class)->reveal(),
            $this->prophesize(Normalizer\NormalizerInterface::class)->reveal()
        );

        $command->setIO($io->reveal());

        $tester = new Console\Tester\CommandTester($command);

        $tester->execute([
            'file' => $composerFile,
            '--indent-style' => $this->faker()->randomElement([
                'space',
                'tab',
            ]),
        ]);

        $this->assertSame(1, $tester->getStatusCode());
        $this->assertFileExists($composerFile);
        $this->assertStringEqualsFile($composerFile, $original);
    }

    /**
     * @dataProvider providerInvalidIndentSize
     *
     * @param $indentSize
     */
    public function testExecuteWithIndentFailsIfIndentSizeIsInvalid($indentSize): void
    {
        $indentStyle = $this->faker()->randomElement(\array_keys($this->indentStyles()));

        $original = $this->composerFileContent();

        $composerFile = $this->pathToComposerFileWithContent($original);

        $io = $this->prophesize(IO\ConsoleIO::class);

        $io
            ->writeError(Argument::is(\sprintf(
                '<error>Indent size needs to be an integer greater than 0, but "%s" is not.</error>',
                $indentSize
            )))
            ->shouldBeCalled();

        $command = new NormalizeCommand(
            $this->prophesize(Factory::class)->reveal(),
            $this->prophesize(Normalizer\NormalizerInterface::class)->reveal()
        );

        $command->setIO($io->reveal());

        $tester = new Console\Tester\CommandTester($command);

        $tester->execute([
            'file' => $composerFile,
            '--indent-size' => $indentSize,
            '--indent-style' => $indentStyle,
        ]);

        $this->assertSame(1, $tester->getStatusCode());
        $this->assertFileExists($composerFile);
        $this->assertStringEqualsFile($composerFile, $original);
    }

    public function providerInvalidIndentSize(): \Generator
    {
        $values = [
            'string-word' => $this->faker()->word,
            'int-zero' => 0,
            'int-negative' => -1,
            'int-zero-casted-to-string' => '0',
            'int-negative-casted-to-string' => '-1',
        ];

        foreach ($values as $key => $value) {
            yield $key => [
                $value,
            ];
        }
    }

    public function testExecuteWithIndentFailsIfIndentStyleIsInvalid(): void
    {
        $indentSize = $this->faker()->numberBetween(1);
        $indentStyle = $this->faker()->sentence;

        $original = $this->composerFileContent();

        $composerFile = $this->pathToComposerFileWithContent($original);

        $io = $this->prophesize(IO\ConsoleIO::class);

        $io
            ->writeError(Argument::is(\sprintf(
                '<error>Indent style needs to be one of "%s", but "%s" is not.</error>',
                \implode('", "', \array_keys($this->indentStyles())),
                $indentStyle
            )))
            ->shouldBeCalled();

        $command = new NormalizeCommand(
            $this->prophesize(Factory::class)->reveal(),
            $this->prophesize(Normalizer\NormalizerInterface::class)->reveal()
        );

        $command->setIO($io->reveal());

        $tester = new Console\Tester\CommandTester($command);

        $tester->execute([
            'file' => $composerFile,
            '--indent-size' => $indentSize,
            '--indent-style' => $indentStyle,
        ]);

        $this->assertSame(1, $tester->getStatusCode());
        $this->assertFileExists($composerFile);
        $this->assertStringEqualsFile($composerFile, $original);
    }

    public function testExecuteFailsIfCreatingComposerFails(): void
    {
        $exceptionMessage = $this->faker()->sentence;

        $composerFile = $this->pathToNonExistentComposerFile();

        $io = $this->prophesize(IO\ConsoleIO::class);

        $io
            ->writeError(Argument::is(\sprintf(
                '<error>%s</error>',
                $exceptionMessage
            )))
            ->shouldBeCalled();

        $factory = $this->prophesize(Factory::class);

        $factory
            ->createComposer(
                Argument::is($io->reveal()),
                Argument::is($composerFile)
            )
            ->shouldBeCalled()
            ->willThrow(new \Exception($exceptionMessage));

        $command = new NormalizeCommand(
            $factory->reveal(),
            $this->prophesize(Normalizer\NormalizerInterface::class)->reveal()
        );

        $command->setIO($io->reveal());

        $tester = new Console\Tester\CommandTester($command);

        $tester->execute([
            'file' => $composerFile,
        ]);

        $this->assertSame(1, $tester->getStatusCode());
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

        $composer = $this->prophesize(Composer::class);

        $factory = $this->prophesize(Factory::class);

        $factory
            ->createComposer(
                Argument::is($io->reveal()),
                Argument::is($composerFile)
            )
            ->shouldBeCalled()
            ->willReturn($composer->reveal());

        $command = new NormalizeCommand(
            $factory->reveal(),
            $this->prophesize(Normalizer\NormalizerInterface::class)->reveal()
        );

        $command->setIO($io->reveal());

        $tester = new Console\Tester\CommandTester($command);

        $tester->execute([
            'file' => $composerFile,
        ]);

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

        $factory = $this->prophesize(Factory::class);

        $factory
            ->createComposer(
                Argument::is($io->reveal()),
                Argument::is($composerFile)
            )
            ->shouldBeCalled()
            ->willReturn($composer->reveal());

        $command = new NormalizeCommand(
            $factory->reveal(),
            $this->prophesize(Normalizer\NormalizerInterface::class)->reveal()
        );

        $command->setIO($io->reveal());

        $tester = new Console\Tester\CommandTester($command);

        $tester->execute([
            'file' => $composerFile,
        ]);

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

        $factory = $this->prophesize(Factory::class);

        $factory
            ->createComposer(
                Argument::is($io->reveal()),
                Argument::is($composerFile)
            )
            ->shouldBeCalled()
            ->willReturn($composer->reveal());

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

        $command = new NormalizeCommand(
            $factory->reveal(),
            $normalizer->reveal()
        );

        $command->setIO($io->reveal());
        $command->setApplication($application->reveal());

        $tester = new Console\Tester\CommandTester($command);

        $tester->execute([
            'file' => $composerFile,
        ]);

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

        $factory = $this->prophesize(Factory::class);

        $factory
            ->createComposer(
                Argument::is($io->reveal()),
                Argument::is($composerFile)
            )
            ->shouldBeCalled()
            ->willReturn($composer->reveal());

        $normalizer = $this->prophesize(Normalizer\NormalizerInterface::class);

        $normalizer
            ->normalize(Argument::is($original))
            ->shouldBeCalled()
            ->willThrow($exception);

        $command = new NormalizeCommand(
            $factory->reveal(),
            $normalizer->reveal()
        );

        $command->setIO($io->reveal());

        $tester = new Console\Tester\CommandTester($command);

        $tester->execute([
            'file' => $composerFile,
        ]);

        $this->assertSame(1, $tester->getStatusCode());
        $this->assertFileExists($composerFile);
        $this->assertStringEqualsFile($composerFile, $original);
    }

    public function testExecuteSucceedsIfLockerIsNotLockedAndComposerFileIsAlreadyNormalized(): void
    {
        $original = $this->composerFileContent();

        $normalized = \json_encode(\json_decode($original));

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

        $factory = $this->prophesize(Factory::class);

        $factory
            ->createComposer(
                Argument::is($io->reveal()),
                Argument::is($composerFile)
            )
            ->shouldBeCalled()
            ->willReturn($composer->reveal());

        $normalizer = $this->prophesize(Normalizer\NormalizerInterface::class);

        $normalizer
            ->normalize(Argument::is($original))
            ->shouldBeCalled()
            ->willReturn($normalized);

        $format = $this->prophesize(Normalizer\Format\FormatInterface::class);

        $sniffer = $this->prophesize(Normalizer\Format\SnifferInterface::class);

        $sniffer
            ->sniff(Argument::is($original))
            ->shouldBeCalled()
            ->willReturn($format->reveal());

        $formatter = $this->prophesize(Normalizer\Format\FormatterInterface::class);

        $formatter
            ->format(
                Argument::is($normalized),
                Argument::is($format->reveal())
            )
            ->shouldBeCalled()
            ->willReturn($original);

        $command = new NormalizeCommand(
            $factory->reveal(),
            $normalizer->reveal(),
            $sniffer->reveal(),
            $formatter->reveal()
        );

        $command->setIO($io->reveal());

        $tester = new Console\Tester\CommandTester($command);

        $tester->execute([
            'file' => $composerFile,
        ]);

        $this->assertSame(0, $tester->getStatusCode());
        $this->assertFileExists($composerFile);
        $this->assertStringEqualsFile($composerFile, $original);
    }

    public function testExecuteSucceedsIfLockerIsLockedAndFreshButComposerFileIsAlreadyNormalized(): void
    {
        $original = $this->composerFileContent();

        $normalized = \json_encode(\json_decode($original));

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

        $factory = $this->prophesize(Factory::class);

        $factory
            ->createComposer(
                Argument::is($io->reveal()),
                Argument::is($composerFile)
            )
            ->shouldBeCalled()
            ->willReturn($composer->reveal());

        $normalizer = $this->prophesize(Normalizer\NormalizerInterface::class);

        $normalizer
            ->normalize(Argument::is($original))
            ->shouldBeCalled()
            ->willReturn($normalized);

        $format = $this->prophesize(Normalizer\Format\FormatInterface::class);

        $sniffer = $this->prophesize(Normalizer\Format\SnifferInterface::class);

        $sniffer
            ->sniff(Argument::is($original))
            ->shouldBeCalled()
            ->willReturn($format->reveal());

        $formatter = $this->prophesize(Normalizer\Format\FormatterInterface::class);

        $formatter
            ->format(
                Argument::is($normalized),
                Argument::is($format->reveal())
            )
            ->shouldBeCalled()
            ->willReturn($original);

        $command = new NormalizeCommand(
            $factory->reveal(),
            $normalizer->reveal(),
            $sniffer->reveal(),
            $formatter->reveal()
        );

        $command->setIO($io->reveal());

        $tester = new Console\Tester\CommandTester($command);

        $tester->execute([
            'file' => $composerFile,
        ]);

        $this->assertSame(0, $tester->getStatusCode());
        $this->assertFileExists($composerFile);
        $this->assertStringEqualsFile($composerFile, $original);
    }

    public function testExecuteWithDryRunSucceedsIfLockerIsLockedAndFreshButComposerFileIsAlreadyNormalized(): void
    {
        $original = $this->composerFileContent();

        $normalized = \json_encode(\json_decode($original));

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

        $factory = $this->prophesize(Factory::class);

        $factory
            ->createComposer(
                Argument::is($io->reveal()),
                Argument::is($composerFile)
            )
            ->shouldBeCalled()
            ->willReturn($composer->reveal());

        $normalizer = $this->prophesize(Normalizer\NormalizerInterface::class);

        $normalizer
            ->normalize(Argument::is($original))
            ->shouldBeCalled()
            ->willReturn($normalized);

        $format = $this->prophesize(Normalizer\Format\FormatInterface::class);

        $sniffer = $this->prophesize(Normalizer\Format\SnifferInterface::class);

        $sniffer
            ->sniff(Argument::is($original))
            ->shouldBeCalled()
            ->willReturn($format->reveal());

        $formatter = $this->prophesize(Normalizer\Format\FormatterInterface::class);

        $formatter
            ->format(
                Argument::is($normalized),
                Argument::is($format->reveal())
            )
            ->shouldBeCalled()
            ->willReturn($original);

        $command = new NormalizeCommand(
            $factory->reveal(),
            $normalizer->reveal(),
            $sniffer->reveal(),
            $formatter->reveal()
        );

        $command->setIO($io->reveal());

        $tester = new Console\Tester\CommandTester($command);

        $tester->execute([
            'file' => $composerFile,
            '--dry-run' => null,
        ]);

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

        $formatted = \json_encode(
            \json_decode($normalized),
            \JSON_PRETTY_PRINT
        );

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

        $factory = $this->prophesize(Factory::class);

        $factory
            ->createComposer(
                Argument::is($io->reveal()),
                Argument::is($composerFile)
            )
            ->shouldBeCalled()
            ->willReturn($composer->reveal());

        $normalizer = $this->prophesize(Normalizer\NormalizerInterface::class);

        $normalizer
            ->normalize(Argument::is($original))
            ->shouldBeCalled()
            ->willReturn($normalized);

        $format = $this->prophesize(Normalizer\Format\FormatInterface::class);

        $sniffer = $this->prophesize(Normalizer\Format\SnifferInterface::class);

        $sniffer
            ->sniff(Argument::is($original))
            ->shouldBeCalled()
            ->willReturn($format->reveal());

        $formatter = $this->prophesize(Normalizer\Format\FormatterInterface::class);

        $formatter
            ->format(
                Argument::is($normalized),
                Argument::is($format->reveal())
            )
            ->shouldBeCalled()
            ->willReturn($formatted);

        $command = new NormalizeCommand(
            $factory->reveal(),
            $normalizer->reveal(),
            $sniffer->reveal(),
            $formatter->reveal()
        );

        $command->setIO($io->reveal());

        $tester = new Console\Tester\CommandTester($command);

        $tester->execute([
            'file' => $composerFile,
        ]);

        $this->assertSame(0, $tester->getStatusCode());
        $this->assertFileExists($composerFile);
        $this->assertStringEqualsFile($composerFile, $formatted);
    }

    public function testExecuteWithDryRunFailsIfLockerIsNotLockedAndComposerFileWasNormalizedSuccessfully(): void
    {
        $original = $this->composerFileContent();

        $normalized = \json_encode(\array_reverse(\json_decode(
            $original,
            true
        )));

        $formatted = \json_encode(
            \json_decode($normalized),
            \JSON_PRETTY_PRINT
        );

        $composerFile = $this->pathToComposerFileWithContent($original);

        $io = $this->prophesize(IO\ConsoleIO::class);

        $io
            ->writeError(Argument::is(\sprintf(
                '<error>%s is not normalized.</error>',
                $composerFile
            )))
            ->shouldBeCalled();

        $io
            ->write(Argument::is([
                '',
                '<fg=green>--- original </>',
                '<fg=red>+++ normalized </>',
                '',
                '<fg=yellow>---------- begin diff ----------</>',
            ]))
            ->shouldBeCalled();

        $io
            ->write(
                Argument::type('array'),
                Argument::is(false)
            )
            ->shouldBeCalled();

        $io
            ->write(Argument::is('<fg=yellow>----------- end diff -----------</>'))
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

        $factory = $this->prophesize(Factory::class);

        $factory
            ->createComposer(
                Argument::is($io->reveal()),
                Argument::is($composerFile)
            )
            ->shouldBeCalled()
            ->willReturn($composer->reveal());

        $normalizer = $this->prophesize(Normalizer\NormalizerInterface::class);

        $normalizer
            ->normalize(Argument::is($original))
            ->shouldBeCalled()
            ->willReturn($normalized);

        $format = $this->prophesize(Normalizer\Format\FormatInterface::class);

        $sniffer = $this->prophesize(Normalizer\Format\SnifferInterface::class);

        $sniffer
            ->sniff(Argument::is($original))
            ->shouldBeCalled()
            ->willReturn($format->reveal());

        $formatter = $this->prophesize(Normalizer\Format\FormatterInterface::class);

        $formatter
            ->format(
                Argument::is($normalized),
                Argument::is($format->reveal())
            )
            ->shouldBeCalled()
            ->willReturn($formatted);

        $command = new NormalizeCommand(
            $factory->reveal(),
            $normalizer->reveal(),
            $sniffer->reveal(),
            $formatter->reveal()
        );

        $command->setIO($io->reveal());

        $tester = new Console\Tester\CommandTester($command);

        $tester->execute([
            'file' => $composerFile,
            '--dry-run' => null,
        ]);

        $this->assertSame(1, $tester->getStatusCode());
        $this->assertFileExists($composerFile);
        $this->assertStringEqualsFile($composerFile, $original);
    }

    public function testExecuteWithIndentSucceedsIfLockerIsNotLockedAndComposerFileWasNormalizedSuccessfully(): void
    {
        $faker = $this->faker();

        $indentSize = (string) $faker->numberBetween(1, 5);
        $indentStyle = $faker->randomElement(\array_keys($this->indentStyles()));

        $indent = \str_repeat(
            $this->indentStyles()[$indentStyle],
            (int) $indentSize
        );

        $original = $this->composerFileContent();

        $normalized = \json_encode(\array_reverse(\json_decode(
            $original,
            true
        )));

        $formatted = \json_encode(
            \json_decode($normalized),
            \JSON_PRETTY_PRINT
        );

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

        $factory = $this->prophesize(Factory::class);

        $factory
            ->createComposer(
                Argument::is($io->reveal()),
                Argument::is($composerFile)
            )
            ->shouldBeCalled()
            ->willReturn($composer->reveal());

        $normalizer = $this->prophesize(Normalizer\NormalizerInterface::class);

        $normalizer
            ->normalize(Argument::is($original))
            ->shouldBeCalled()
            ->willReturn($normalized);

        $configuredFormat = $this->prophesize(Normalizer\Format\FormatInterface::class);

        $sniffedFormat = $this->prophesize(Normalizer\Format\FormatInterface::class);

        $sniffedFormat
            ->withIndent(Argument::is($indent))
            ->shouldBeCalled()
            ->willReturn($configuredFormat->reveal());

        $sniffer = $this->prophesize(Normalizer\Format\SnifferInterface::class);

        $sniffer
            ->sniff(Argument::is($original))
            ->shouldBeCalled()
            ->willReturn($sniffedFormat->reveal());

        $formatter = $this->prophesize(Normalizer\Format\FormatterInterface::class);

        $formatter
            ->format(
                Argument::is($normalized),
                Argument::is($configuredFormat->reveal())
            )
            ->shouldBeCalled()
            ->willReturn($formatted);

        $command = new NormalizeCommand(
            $factory->reveal(),
            $normalizer->reveal(),
            $sniffer->reveal(),
            $formatter->reveal()
        );

        $command->setIO($io->reveal());

        $tester = new Console\Tester\CommandTester($command);

        $tester->execute([
            'file' => $composerFile,
            '--indent-size' => $indentSize,
            '--indent-style' => $indentStyle,
        ]);

        $this->assertSame(0, $tester->getStatusCode());
        $this->assertFileExists($composerFile);
        $this->assertStringEqualsFile($composerFile, $formatted);
    }

    public function testExecuteFailsIfLockerIsNotLockedAndComposerFileWasNormalizedSuccessfullyWithIndent(): void
    {
        $faker = $this->faker();

        $indentSize = (string) $faker->numberBetween(1, 5);
        $indentStyle = $faker->randomElement(\array_keys($this->indentStyles()));

        $indent = \str_repeat(
            $this->indentStyles()[$indentStyle],
            (int) $indentSize
        );

        $original = $this->composerFileContent();

        $normalized = \json_encode(\array_reverse(\json_decode(
            $original,
            true
        )));

        $formatted = \json_encode(
            \json_decode($normalized),
            \JSON_PRETTY_PRINT
        );

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

        $factory = $this->prophesize(Factory::class);

        $factory
            ->createComposer(
                Argument::is($io->reveal()),
                Argument::is($composerFile)
            )
            ->shouldBeCalled()
            ->willReturn($composer->reveal());

        $normalizer = $this->prophesize(Normalizer\NormalizerInterface::class);

        $normalizer
            ->normalize(Argument::is($original))
            ->shouldBeCalled()
            ->willReturn($normalized);

        $configuredFormat = $this->prophesize(Normalizer\Format\FormatInterface::class);

        $sniffedFormat = $this->prophesize(Normalizer\Format\FormatInterface::class);

        $sniffedFormat
            ->withIndent(Argument::is($indent))
            ->shouldBeCalled()
            ->willReturn($configuredFormat->reveal());

        $sniffer = $this->prophesize(Normalizer\Format\SnifferInterface::class);

        $sniffer
            ->sniff(Argument::is($original))
            ->shouldBeCalled()
            ->willReturn($sniffedFormat->reveal());

        $formatter = $this->prophesize(Normalizer\Format\FormatterInterface::class);

        $formatter
            ->format(
                Argument::is($normalized),
                Argument::is($configuredFormat->reveal())
            )
            ->shouldBeCalled()
            ->willReturn($formatted);

        $command = new NormalizeCommand(
            $factory->reveal(),
            $normalizer->reveal(),
            $sniffer->reveal(),
            $formatter->reveal()
        );

        $command->setIO($io->reveal());

        $tester = new Console\Tester\CommandTester($command);

        $tester->execute([
            'file' => $composerFile,
            '--indent-size' => $indentSize,
            '--indent-style' => $indentStyle,
        ]);

        $this->assertSame(0, $tester->getStatusCode());
        $this->assertFileExists($composerFile);
        $this->assertStringEqualsFile($composerFile, $formatted);
    }

    public function testExecuteSucceedsIfLockerIsLockedAndLockerCouldBeUpdatedAfterNormalization(): void
    {
        $original = $this->composerFileContent();

        $normalized = \json_encode(\array_reverse(\json_decode(
            $original,
            true
        )));

        $formatted = \json_encode(
            \json_decode($normalized),
            \JSON_PRETTY_PRINT
        );

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

        $factory = $this->prophesize(Factory::class);

        $factory
            ->createComposer(
                Argument::is($io->reveal()),
                Argument::is($composerFile)
            )
            ->shouldBeCalled()
            ->willReturn($composer->reveal());

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

        $format = $this->prophesize(Normalizer\Format\FormatInterface::class);

        $sniffer = $this->prophesize(Normalizer\Format\SnifferInterface::class);

        $sniffer
            ->sniff(Argument::is($original))
            ->shouldBeCalled()
            ->willReturn($format);

        $formatter = $this->prophesize(Normalizer\Format\FormatterInterface::class);

        $formatter
            ->format(
                Argument::is($normalized),
                Argument::is($format->reveal())
            )
            ->shouldBeCalled()
            ->willReturn($formatted);

        $command = new NormalizeCommand(
            $factory->reveal(),
            $normalizer->reveal(),
            $sniffer->reveal(),
            $formatter->reveal()
        );

        $command->setIO($io->reveal());
        $command->setApplication($application->reveal());

        $tester = new Console\Tester\CommandTester($command);

        $tester->execute([
            'file' => $composerFile,
        ]);

        $this->assertSame(0, $tester->getStatusCode());
        $this->assertFileExists($composerFile);
        $this->assertStringEqualsFile($composerFile, $formatted);
    }

    public function testExecuteDefaultsToUsingComposerFileFromCurrentDirectory(): void
    {
        $original = $this->composerFileContent();

        $normalized = \json_encode(\array_reverse(\json_decode(
            $original,
            true
        )));

        $formatted = \json_encode(
            \json_decode($normalized),
            \JSON_PRETTY_PRINT
        );

        $composerFile = $this->pathToComposerFileWithContent($original);

        $this->useComposerFile($composerFile);

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

        $factory = $this->prophesize(Factory::class);

        $factory
            ->createComposer(
                Argument::is($io->reveal()),
                Argument::is($composerFile)
            )
            ->shouldBeCalled()
            ->willReturn($composer->reveal());

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

        $format = $this->prophesize(Normalizer\Format\FormatInterface::class);

        $sniffer = $this->prophesize(Normalizer\Format\SnifferInterface::class);

        $sniffer
            ->sniff(Argument::is($original))
            ->shouldBeCalled()
            ->willReturn($format);

        $formatter = $this->prophesize(Normalizer\Format\FormatterInterface::class);

        $formatter
            ->format(
                Argument::is($normalized),
                Argument::is($format->reveal())
            )
            ->shouldBeCalled()
            ->willReturn($formatted);

        $command = new NormalizeCommand(
            $factory->reveal(),
            $normalizer->reveal(),
            $sniffer->reveal(),
            $formatter->reveal()
        );

        $command->setIO($io->reveal());
        $command->setApplication($application->reveal());

        $tester = new Console\Tester\CommandTester($command);

        $tester->execute([]);

        $this->assertSame(0, $tester->getStatusCode());
        $this->assertFileExists($composerFile);
        $this->assertStringEqualsFile($composerFile, $formatted);
    }

    public function testExecuteSucceedsIfLockerIsLockedButSkipsUpdatingLockerIfNoUpdateLockOptionIsUsed(): void
    {
        $original = $this->composerFileContent();

        $normalized = \json_encode(\array_reverse(\json_decode(
            $original,
            true
        )));

        $formatted = \json_encode(
            \json_decode($normalized),
            \JSON_PRETTY_PRINT
        );

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

        $factory = $this->prophesize(Factory::class);

        $factory
            ->createComposer(
                Argument::is($io->reveal()),
                Argument::is($composerFile)
            )
            ->shouldBeCalled()
            ->willReturn($composer->reveal());

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

        $format = $this->prophesize(Normalizer\Format\FormatInterface::class);

        $sniffer = $this->prophesize(Normalizer\Format\SnifferInterface::class);

        $sniffer
            ->sniff(Argument::is($original))
            ->shouldBeCalled()
            ->willReturn($format);

        $formatter = $this->prophesize(Normalizer\Format\FormatterInterface::class);

        $formatter
            ->format(
                Argument::is($normalized),
                Argument::is($format->reveal())
            )
            ->shouldBeCalled()
            ->willReturn($formatted);

        $command = new NormalizeCommand(
            $factory->reveal(),
            $normalizer->reveal(),
            $sniffer->reveal(),
            $formatter->reveal()
        );

        $command->setIO($io->reveal());
        $command->setApplication($application->reveal());

        $tester = new Console\Tester\CommandTester($command);

        $tester->execute([
            'file' => $composerFile,
            '--no-update-lock' => null,
        ]);

        $this->assertSame(0, $tester->getStatusCode());
        $this->assertFileExists($composerFile);
        $this->assertStringEqualsFile($composerFile, $formatted);
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

        return $composerFile;
    }

    /**
     * Returns the path to a non-existent composer.json.
     *
     * @return string
     */
    private function pathToNonExistentComposerFile(): string
    {
        return $this->pathToComposerFile();
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
    private function useComposerFile(string $composerFile): void
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

    /**
     * @return array
     */
    private function indentStyles(): array
    {
        return [
            'space' => ' ',
            'tab' => "\t",
        ];
    }
}
