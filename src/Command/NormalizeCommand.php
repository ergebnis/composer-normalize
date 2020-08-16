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
use Composer\Console\Application;
use Composer\Factory;
use Composer\IO;
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
                'no-check-lock',
                null,
                Console\Input\InputOption::VALUE_NONE,
                'Do not check if lock file is up to date'
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
            $indent = self::indentFrom($input);
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

        $composer = $this->factory->createComposer(
            $io,
            $composerFile
        );

        if (false === $input->getOption('dry-run') && !\is_writable($composerFile)) {
            $io->writeError(\sprintf(
                '<error>%s is not writable.</error>',
                $composerFile
            ));

            return 1;
        }

        $locker = $composer->getLocker();

        if (false === $input->getOption('no-check-lock') && $locker->isLocked() && !$locker->isFresh()) {
            $io->writeError('<error>The lock file is not up to date with the latest changes in composer.json, it is recommended that you run `composer update --lock`.</error>');

            return 1;
        }

        /** @var string $encoded */
        $encoded = \file_get_contents($composerFile);

        $json = Normalizer\Json::fromEncoded($encoded);

        try {
            $normalized = $this->normalizer->normalize($json);
        } catch (Normalizer\Exception\OriginalInvalidAccordingToSchemaException $exception) {
            $io->writeError('<error>Original composer.json does not match the expected JSON schema:</error>');

            self::showValidationErrors(
                $io,
                ...$exception->errors()
            );

            return 1;
        } catch (Normalizer\Exception\NormalizedInvalidAccordingToSchemaException $exception) {
            $io->writeError('<error>Normalized composer.json does not match the expected JSON schema:</error>');

            self::showValidationErrors(
                $io,
                ...$exception->errors()
            );

            return 1;
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

            $diff = $this->differ->diff(
                $json->encoded(),
                $formatted->encoded()
            );

            $io->write([
                '',
                '<fg=yellow>---------- begin diff ----------</>',
                self::formatDiff($diff),
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

        $application = new Application();

        $application->setAutoExit(false);

        return self::updateLockerInWorkingDirectory(
            $application,
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
    private static function indentFrom(Console\Input\InputInterface $input): ?Normalizer\Format\Indent
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

    private static function showValidationErrors(IO\IOInterface $io, string ...$errors): void
    {
        foreach ($errors as $error) {
            $io->writeError(\sprintf(
                '<error>- %s</error>',
                $error
            ));
        }

        $io->writeError('<warning>See https://getcomposer.org/doc/04-schema.md for details on the schema</warning>');
    }

    private static function formatDiff(string $diff): string
    {
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
     * @see https://getcomposer.org/doc/03-cli.md#update
     *
     * @param Console\Application            $application
     * @param Console\Output\OutputInterface $output
     * @param string                         $workingDirectory
     *
     * @throws \Exception
     *
     * @return int
     */
    private static function updateLockerInWorkingDirectory(
        Console\Application $application,
        Console\Output\OutputInterface $output,
        string $workingDirectory
    ): int {
        return $application->run(
            new Console\Input\ArrayInput([
                'command' => 'update',
                '--ignore-platform-reqs' => true,
                '--lock' => true,
                '--no-autoloader' => true,
                '--no-plugins' => true,
                '--no-scripts' => true,
                '--working-dir' => $workingDirectory,
            ]),
            $output
        );
    }
}
