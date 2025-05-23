<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018-2025 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/composer-normalize
 */

namespace Ergebnis\Composer\Normalize\Test\Util;

final class Directory
{
    private string $path;
    private bool $exists;

    private function __construct(string $path)
    {
        $this->path = $path;
        $this->exists = \file_exists($path) && \is_dir($path);
    }

    public static function fromPath(string $path): self
    {
        return new self($path);
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
