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

namespace Localheinz\Composer\Normalize\Test\Util;

final class Directory
{
    /**
     * @var string
     */
    private $path;

    /**
     * @var bool
     */
    private $exists;

    private function __construct()
    {
    }

    public static function fromPath(string $path): self
    {
        $directory = new self();

        $directory->path = $path;
        $directory->exists = \file_exists($path) && \is_dir($path);

        return $directory;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function exists(): bool
    {
        return $this->exists;
    }
}
