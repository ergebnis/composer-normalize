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
     * @var Normalizer\NormalizerInterface
     */
    private $normalizer;

    public function __construct(Normalizer\NormalizerInterface $normalizer)
    {
        parent::__construct('normalize');

        $this->normalizer = $normalizer;
    }

    protected function configure()
    {
        $this->setDescription('Normalizes composer.json according to its JSON schema (https://getcomposer.org/schema.json).');
    }

    protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output): int
    {
        $file = Factory::getComposerFile();

        $io = $this->getIO();

        if (!\file_exists($file)) {
            $io->writeError(\sprintf(
                '<error>%s not found.</error>',
                $file
            ));

            return 1;
        }

        if (!\is_readable($file)) {
            $io->writeError(\sprintf(
                '<error>%s is not readable.</error>',
                $file
            ));

            return 1;
        }

        if (!\is_writable($file)) {
            $io->writeError(\sprintf(
                '<error>%s is not writable.</error>',
                $file
            ));

            return 1;
        }

        $composer = $this->getComposer();

        $locker = $composer->getLocker();

        if ($locker->isLocked() && !$locker->isFresh()) {
            $io->writeError('<error>The lock file is not up to date with the latest changes in composer.json, it is recommended that you run `composer update`.</error>');

            return 1;
        }

        $json = \file_get_contents($file);

        try {
            $normalized = $this->normalizer->normalize($json);
        } catch (\InvalidArgumentException $exception) {
            $io->writeError(\sprintf(
                '<error>%s</error>',
                $exception->getMessage()
            ));

            return 1;
        } catch (\RuntimeException $exception) {
            $io->writeError(\sprintf(
                '<error>%s</error>',
                $exception->getMessage()
            ));

            return 1;
        }

        if ($json === $normalized) {
            $io->write(\sprintf(
                '<info>%s is already normalized.</info>',
                $file
            ));

            return 0;
        }

        \file_put_contents($file, $normalized);

        $io->write(\sprintf(
            '<info>Successfully normalized %s.</info>',
            $file
        ));

        if (!$locker->isLocked()) {
            return 0;
        }

        return $this->updateLocker();
    }

    private function updateLocker(): int
    {
        return $this->getApplication()->run(
            new Console\Input\StringInput('update --lock --no-plugins'),
            new Console\Output\NullOutput()
        );
    }
}
