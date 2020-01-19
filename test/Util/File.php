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

namespace Ergebnis\Composer\Normalize\Test\Util;

final class File
{
    /**
     * @var string
     */
    private $path;

    /**
     * @var bool
     */
    private $exists = false;

    /**
     * @var null|string
     */
    private $contents;

    private function __construct()
    {
    }

    public static function fromPath(string $path): self
    {
        $file = new self();

        $file->path = $path;

        if (\file_exists($path)) {
            $file->exists = true;

            $contents = \file_get_contents($path);

            if (\is_string($contents)) {
                $file->contents = $contents;
            }
        }

        return $file;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function exists(): bool
    {
        return $this->exists;
    }

    public function contents(): string
    {
        if (false === $this->exists || null === $this->contents) {
            throw new \BadMethodCallException(\sprintf(
                'File at "%s" did not exist or was not readable at the time of creation.',
                $this->path
            ));
        }

        return $this->contents;
    }
}
