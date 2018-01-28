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

namespace Localheinz\Composer\Normalize\Command;

use Composer\Command;
use Composer\Factory;
use Localheinz\Json\Normalizer;
use Symfony\Component\Console;

final class NormalizeCommand extends Command\BaseCommand
{
    /**
     * @var array
     */
    private static $indentStyles = [
        'space' => ' ',
        'tab' => "\t",
    ];

    /**
     * @var Normalizer\NormalizerInterface
     */
    private $normalizer;

    /**
     * @var Normalizer\Format\SnifferInterface
     */
    private $sniffer;

    /**
     * @var Normalizer\Format\FormatterInterface
     */
    private $formatter;

    public function __construct(
        Normalizer\NormalizerInterface $normalizer,
        Normalizer\Format\SnifferInterface $sniffer = null,
        Normalizer\Format\FormatterInterface $formatter = null
    ) {
        parent::__construct('normalize');

        $this->normalizer = $normalizer;
        $this->sniffer = $sniffer ?: new Normalizer\Format\Sniffer();
        $this->formatter = $formatter ?: new Normalizer\Format\Formatter();
    }

    protected function configure(): void
    {
        $this->setDescription('Normalizes composer.json according to its JSON schema (https://getcomposer.org/schema.json).');
        $this->setDefinition([
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

        try {
            $composerFile = $this->composerFile();
        } catch (\RuntimeException $exception) {
            $io->writeError(\sprintf(
                '<error>%s</error>',
                $exception->getMessage()
            ));

            return 1;
        }

        $locker = $this->getComposer()->getLocker();

        if ($locker->isLocked() && !$locker->isFresh()) {
            $io->writeError('<error>The lock file is not up to date with the latest changes in composer.json, it is recommended that you run `composer update`.</error>');

            return 1;
        }

        $json = \file_get_contents($composerFile);

        try {
            $normalized = $this->normalizer->normalize($json);
        } catch (\InvalidArgumentException $exception) {
            $io->writeError(\sprintf(
                '<error>%s</error>',
                $exception->getMessage()
            ));

            return $this->validateComposerFile($output);
        } catch (\RuntimeException $exception) {
            $io->writeError(\sprintf(
                '<error>%s</error>',
                $exception->getMessage()
            ));

            return 1;
        }

        $format = $this->sniffer->sniff($json);

        if (null !== $indent) {
            $format = $format->withIndent($indent);
        }

        $formatted = $this->formatter->format(
            $normalized,
            $format
        );

        if ($json === $formatted) {
            $io->write(\sprintf(
                '<info>%s is already normalized.</info>',
                $composerFile
            ));

            return 0;
        }

        \file_put_contents($composerFile, $formatted);

        $io->write(\sprintf(
            '<info>Successfully normalized %s.</info>',
            $composerFile
        ));

        $noUpdateLock = $input->getOption('no-update-lock');

        if (!$noUpdateLock && $locker->isLocked()) {
            $io->write('<info>Updating lock file.</info>');

            return $this->updateLocker($output);
        }

        return 0;
    }

    /**
     * @param Console\Input\InputInterface $input
     *
     * @throws \RuntimeException
     *
     * @return null|string
     */
    private function indentFrom(Console\Input\InputInterface $input): ?string
    {
        $indentSize = $input->getOption('indent-size');
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

        if ((string) (int) $indentSize !== (string) $indentSize || 1 > $indentSize) {
            throw new \RuntimeException(\sprintf(
                'Indent size needs to be an integer greater than 0, but "%s" is not.',
                $indentSize
            ));
        }

        if (!\array_key_exists($indentStyle, self::$indentStyles)) {
            throw new \RuntimeException(\sprintf(
                'Indent style needs to be one of "%s", but "%s" is not.',
                \implode('", "', \array_keys(self::$indentStyles)),
                $indentStyle
            ));
        }

        return \str_repeat(
            self::$indentStyles[$indentStyle],
            (int) $indentSize
        );
    }

    /**
     * @throws \RuntimeException
     *
     * @return string
     */
    private function composerFile(): string
    {
        $composerFile = Factory::getComposerFile();

        if (!\file_exists($composerFile)) {
            throw new \RuntimeException(\sprintf(
                '%s not found.',
                $composerFile
            ));
        }

        if (!\is_readable($composerFile)) {
            throw new \RuntimeException(\sprintf(
                '%s is not readable.',
                $composerFile
            ));
        }

        if (!\is_writable($composerFile)) {
            throw new \RuntimeException(\sprintf(
                '%s is not writable.',
                $composerFile
            ));
        }

        return $composerFile;
    }

    /**
     * @see https://getcomposer.org/doc/03-cli.md#validate
     *
     * @param Console\Output\OutputInterface $output
     *
     * @throws \Exception
     *
     * @return int
     */
    private function validateComposerFile(Console\Output\OutputInterface $output): int
    {
        return $this->getApplication()->run(
            new Console\Input\StringInput('validate --no-check-all --no-check-lock --no-check-publish --strict'),
            $output
        );
    }

    /**
     * @see https://getcomposer.org/doc/03-cli.md#update
     *
     * @param Console\Output\OutputInterface $output
     *
     * @throws \Exception
     *
     * @return int
     */
    private function updateLocker(Console\Output\OutputInterface $output): int
    {
        return $this->getApplication()->run(
            new Console\Input\StringInput('update --lock --no-autoloader --no-plugins --no-scripts --no-suggest'),
            $output
        );
    }
}
