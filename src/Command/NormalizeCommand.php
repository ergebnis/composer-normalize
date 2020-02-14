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

namespace Ergebnis\Composer\Normalize\Command;

use Composer\Command;
use Composer\Factory;
use Ergebnis\Composer\Normalize\Exception;
use Ergebnis\Json\Normalizer;
use Localheinz\Diff;
use Symfony\Component\Console;

/**
 * @internal
 */
final class NormalizeCommand extends Command\BaseCommand
{
    /**
     * @var array<string, string>
     */
    private static $indentStyles = [
        'space' => ' ',
        'tab' => "\t",
    ];

    private $factory;

    private $normalizer;

    private $formatter;

    private $differ;

    public function __construct(
        Factory $factory,
        Normalizer\NormalizerInterface $normalizer,
        Normalizer\Format\FormatterInterface $formatter,
        Diff\Differ $differ
    ) {
        parent::__construct('normalize');

        $this->factory = $factory;
        $this->normalizer = $normalizer;
        $this->formatter = $formatter;
        $this->differ = $differ;
    }

    protected function configure(): void
    {
        $this->setDescription('Normalizes composer.json according to its JSON schema (https://getcomposer.org/schema.json).');
        $this->setDefinition([
            new Console\Input\InputArgument(
                'file',
                Console\Input\InputArgument::OPTIONAL,
                'Path to composer.json file'
            ),
            new Console\Input\InputOption(
                'diff',
                null,
                Console\Input\InputOption::VALUE_NONE,
                'Show the results of normalizing'
            ),
            new Console\Input\InputOption(
                'dry-run',
                null,
                Console\Input\InputOption::VALUE_NONE,
                'Show the results of normalizing, but do not modify any files'
            ),
            new Console\Input\InputOption(
                'indent-size',
                null,
                Console\Input\InputOption::VALUE_REQUIRED,
                'Indent size (an integer greater than 0); should be used with the --indent-style option'
            ),
            new Console\Input\InputOption(
                'indent-style',
                null,
                Console\Input\InputOption::VALUE_REQUIRED,
                \sprintf(
                    'Indent style (one of "%s"); should be used with the --indent-size option',
                    \implode('", "', \array_keys(self::$indentStyles))
                )
            ),
            new Console\Input\InputOption(
                'no-update-lock',
                null,
                Console\Input\InputOption::VALUE_NONE,
                'Do not update lock file if it exists'
            ),
        ]);
    }

    protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output): int
    {
        $io = $this->getIO();

        try {
            $indent = $this->indentFrom($input);
        } catch (\RuntimeException $exception) {
            $io->writeError(\sprintf(
                '<error>%s</error>',
                $exception->getMessage()
            ));

            return 1;
        }

        $composerFile = $input->getArgument('file');

        if (null === $composerFile) {
            $composerFile = Factory::getComposerFile();
        }

        try {
            $composer = $this->factory->createComposer(
                $io,
                $composerFile
            );
        } catch (\Exception $exception) {
            $io->writeError(\sprintf(
                '<error>%s</error>',
                $exception->getMessage()
            ));

            return 1;
        }

        if (false === $input->getOption('dry-run') && !\is_writable($composerFile)) {
            $io->writeError(\sprintf(
                '<error>%s is not writable.</error>',
                $composerFile
            ));

            return 1;
        }

        $locker = $composer->getLocker();

        if ($locker->isLocked() && !$locker->isFresh()) {
            $io->writeError('<error>The lock file is not up to date with the latest changes in composer.json, it is recommended that you run `composer update --lock`.</error>');

            return 1;
        }

        /** @var string $encoded */
        $encoded = \file_get_contents($composerFile);

        $json = Normalizer\Json::fromEncoded($encoded);

        try {
            $normalized = $this->normalizer->normalize($json);
        } catch (Normalizer\Exception\OriginalInvalidAccordingToSchemaException $exception) {
            $io->writeError(\sprintf(
                '<error>%s</error>',
                $exception->getMessage()
            ));

            return $this->validateComposerFile(
                $output,
                $composerFile
            );
        } catch (\RuntimeException $exception) {
            $io->writeError(\sprintf(
                '<error>%s</error>',
                $exception->getMessage()
            ));

            return 1;
        }

        $format = $json->format();

        if (null !== $indent) {
            $format = $format->withIndent($indent);
        }

        $formatted = $this->formatter->format(
            $normalized,
            $format
        );

        if ($json->encoded() === $formatted->encoded()) {
            $io->write(\sprintf(
                '<info>%s is already normalized.</info>',
                $composerFile
            ));

            return 0;
        }

        if (true === $input->getOption('diff') || true === $input->getOption('dry-run')) {
            $io->writeError(\sprintf(
                '<error>%s is not normalized.</error>',
                $composerFile
            ));

            $io->write([
                '',
                '<fg=yellow>---------- begin diff ----------</>',
            ]);

            $io->write($this->diff(
                $json->encoded(),
                $formatted->encoded()
            ));

            $io->write([
                '<fg=yellow>----------- end diff -----------</>',
                '',
            ]);
        }

        if (true === $input->getOption('dry-run')) {
            return 1;
        }

        \file_put_contents($composerFile, $formatted);

        $io->write(\sprintf(
            '<info>Successfully normalized %s.</info>',
            $composerFile
        ));

        if (true === $input->getOption('no-update-lock') || false === $locker->isLocked()) {
            return 0;
        }

        $io->write('<info>Updating lock file.</info>');

        $this->resetComposer();

        return $this->updateLockerInWorkingDirectory(
            $output,
            \dirname($composerFile)
        );
    }

    /**
     * @param Console\Input\InputInterface $input
     *
     * @throws \RuntimeException
     *
     * @return null|Normalizer\Format\Indent
     */
    private function indentFrom(Console\Input\InputInterface $input): ?Normalizer\Format\Indent
    {
        /** @var null|string $indentSize */
        $indentSize = $input->getOption('indent-size');

        /** @var null|string $indentStyle */
        $indentStyle = $input->getOption('indent-style');

        if (null === $indentSize && null === $indentStyle) {
            return null;
        }

        if (null === $indentSize) {
            throw new \RuntimeException('When using the indent-style option, an indent size needs to be specified using the indent-size option.');
        }

        if (null === $indentStyle) {
            throw new \RuntimeException(\sprintf(
                'When using the indent-size option, an indent style (one of "%s") needs to be specified using the indent-style option.',
                \implode('", "', \array_keys(self::$indentStyles))
            ));
        }

        if ((string) (int) $indentSize !== $indentSize || 1 > $indentSize) {
            throw new \RuntimeException(\sprintf(
                'Indent size needs to be an integer greater than 0, but "%s" is not.',
                $indentSize
            ));
        }

        try {
            $indent = Normalizer\Format\Indent::fromSizeAndStyle(
                (int) $indentSize,
                $indentStyle
            );
        } catch (Normalizer\Exception\InvalidIndentSizeException $exception) {
            throw new \RuntimeException(\sprintf(
                'Indent size needs to be an integer greater than %d, but "%s" is not.',
                $exception->minimumSize(),
                $exception->size()
            ));
        } catch (Normalizer\Exception\InvalidIndentStyleException $exception) {
            throw new \RuntimeException(\sprintf(
                'Indent style needs to be one of "%s", but "%s" is not.',
                \implode('", "', \array_keys(self::$indentStyles)),
                $indentStyle
            ));
        }

        return $indent;
    }

    /**
     * @param string $before
     * @param string $after
     *
     * @return string
     */
    private function diff(string $before, string $after): string
    {
        $diff = $this->differ->diff(
            $before,
            $after
        );

        $lines = \explode(
            "\n",
            $diff
        );

        $formatted = \array_map(static function (string $line): string {
            $replaced = \preg_replace(
                [
                    '/^(\+.*)$/',
                    '/^(-.*)$/',
                ],
                [
                    '<fg=green>$1</>',
                    '<fg=red>$1</>',
                ],
                $line
            );

            if (!\is_string($replaced)) {
                throw Exception\ShouldNotHappen::create();
            }

            return $replaced;
        }, $lines);

        return \implode(
            "\n",
            $formatted
        );
    }

    /**
     * @see https://getcomposer.org/doc/03-cli.md#validate
     *
     * @param Console\Output\OutputInterface $output
     * @param string                         $composerFile
     *
     * @throws \Exception
     *
     * @return int
     */
    private function validateComposerFile(Console\Output\OutputInterface $output, string $composerFile): int
    {
        /** @var Console\Application $application */
        $application = $this->getApplication();

        return $application->run(
            new Console\Input\ArrayInput([
                'command' => 'validate',
                'file' => $composerFile,
                '--no-check-all' => true,
                '--no-check-lock' => true,
                '--no-check-publish' => true,
                //'--strict' => true,
            ]),
            $output
        );
    }

    /**
     * @see https://getcomposer.org/doc/03-cli.md#update
     *
     * @param Console\Output\OutputInterface $output
     * @param string                         $workingDirectory
     *
     * @throws \Exception
     *
     * @return int
     */
    private function updateLockerInWorkingDirectory(Console\Output\OutputInterface $output, string $workingDirectory): int
    {
        /** @var Console\Application $application */
        $application = $this->getApplication();

        return $application->run(
            new Console\Input\ArrayInput([
                'command' => 'update',
                '--lock' => true,
                '--no-autoloader' => true,
                '--no-plugins' => true,
                '--no-scripts' => true,
                '--no-suggest' => true,
                '--working-dir' => $workingDirectory,
            ]),
            $output
        );
    }
}
