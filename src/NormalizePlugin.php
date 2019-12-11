<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/localheinz/composer-normalize
 */

namespace Localheinz\Composer\Normalize;

use Composer\Composer;
use Composer\Factory;
use Composer\IO;
use Composer\IO\IOInterface;
use Composer\Plugin;
use Localheinz\Composer\Json\Normalizer;
use Localheinz\Composer\Normalize\Command\SchemaUriResolver;

final class NormalizePlugin implements Plugin\Capability\CommandProvider, Plugin\Capable, Plugin\PluginInterface
{
    public function activate(Composer $composer, IO\IOInterface $io): void
    {
    }

    public function deactivate(Composer $composer, IOInterface $io): void
    {
    }

    public function uninstall(Composer $composer, IOInterface $io): void
    {
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
            new Command\NormalizeCommand(
                new Factory(),
                new Normalizer\ComposerJsonNormalizer(SchemaUriResolver::resolve())
            ),
        ];
    }
}
