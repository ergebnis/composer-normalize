<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018-2024 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/composer-normalize
 */

namespace Ergebnis\Composer\Normalize\Test\Util;

final class Directory
{
    private readonly bool $exists;

    private function __construct(private readonly string $path)
    {
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
