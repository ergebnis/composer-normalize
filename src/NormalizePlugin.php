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

namespace Localheinz\Composer\Normalize;

use Composer\Composer;
use Composer\IO;
use Composer\Plugin;
use Localheinz\Json\Normalizer\AutoFormatNormalizer;
use Localheinz\Json\Normalizer\ChainNormalizer;

final class NormalizePlugin implements Plugin\PluginInterface, Plugin\Capable, Plugin\Capability\CommandProvider
{
    /**
     * @var Composer
     */
    private $composer;

    /**
     * @var IO\IOInterface
     */
    private $io;

    public function activate(Composer $composer, IO\IOInterface $io)
    {
        $this->composer = $composer;
        $this->io = $io;
    }

    public function getCapabilities(): array
    {
        return [
            Plugin\Capability\CommandProvider::class => self::class,
        ];
    }

    public function getCommands(): array
    {
        return [
            new Command\NormalizeCommand(new AutoFormatNormalizer(new ChainNormalizer(
                new Normalizer\ConfigHashNormalizer()
            ))),
        ];
    }
}
