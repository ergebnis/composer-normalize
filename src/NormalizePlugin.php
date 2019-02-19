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

use Composer\Cache;
use Composer\Composer;
use Composer\Factory;
use Composer\IO;
use Composer\Plugin;
use Localheinz\Composer\Json\Normalizer;

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

    /**
     * @var string
     */
    private $composerJsonSchema;

    /**
     * {@inheritdoc}
     */
    public function activate(Composer $composer, IO\IOInterface $io): void
    {
        $this->composer = $composer;
        $this->io = $io;

        // Statically cache the composerJsonSchema if any.
        $this->composerJsonSchema = $this->composerJsonSchemaResolver();
    }

    /**
     * {@inheritdoc}
     */
    public function getCapabilities(): array
    {
        return [
            Plugin\Capability\CommandProvider::class => self::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getCommands(): array
    {
        $file = '';

        if ('' !== $local = $this->composerJsonSchemaResolver()) {
            $file = 'file://' . \realpath($local);
        }

        return [
            new Command\NormalizeCommand(
                new Factory(),
                new Normalizer\ComposerJsonNormalizer($file)
            ),
        ];
    }

    /**
     * TODO.
     */
    private function composerJsonSchemaResolver(): string
    {
        static $composerJsonSchema;

        if (isset($composerJsonSchema)) {
            return $composerJsonSchema;
        }

        if ('' !== $file = $this->getAndCacheRemoteSchemaFile()) {
            $composerJsonSchema = $file;

            return $composerJsonSchema;
        }

        if ('' !== $file = $this->getLocalSchemaFromVendor()) {
            $composerJsonSchema = $file;

            return $composerJsonSchema;
        }

        if ('' !== $file = $this->getLocalSchema()) {
            $composerJsonSchema = $file;

            return $composerJsonSchema;
        }

        return '';
    }

    /**
     * TODO.
     */
    private function getLocalSchema(): string
    {
        $file = __DIR__ . '/../res/composer-schema.json';

        return \file_exists($file) ?
            $file :
            '';
    }

    /**
     * TODO.
     */
    private function getLocalSchemaFromVendor(): string
    {
        $file = __DIR__ . '/../../composer/res/composer-schema.json';

        return \file_exists($file) ?
            $file :
            '';
    }

    /**
     * TODO.
     */
    private function getAndCacheRemoteSchemaFile(): string
    {
        $config = $this->composer->getConfig();

        $cache = new Cache($this->io, $config->get('cache-dir'));

        // Official url of the composer.json schema.
        $url = 'https://getcomposer.org/schema.json';

        $read = $cache->read('composer-schema.json');
        $tmpFilename = $config->get('cache-dir') . '/composer-schema.json';

        if (false === $read) {
            $rfs = Factory::createRemoteFilesystem($this->io, $config);
            $rfs->copy($url, $url, $tmpFilename, false, []);
            $cache->copyFrom('composer-schema.json', $tmpFilename);
            $read = $cache->read('composer-schema.json');
        }

        if (false !== $read) {
            return $tmpFilename;
        }

        return '';
    }
}
