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

final class File
{
    private ?string $contents;
    private bool $exists;
    private string $path;

    private function __construct(
        string $path,
        bool $exists,
        ?string $contents
    ) {
        $this->path = $path;
        $this->exists = $exists;
        $this->contents = $contents;
    }

    public static function fromPath(string $path): self
    {
        if (!\file_exists($path)) {
            return new self(
                $path,
                false,
                null,
            );
        }

        $contents = \file_get_contents($path);

        if (!\is_string($contents)) {
            return new self(
                $path,
                true,
                null,
            );
        }

        return new self(
            $path,
            true,
            $contents,
        );
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
                $this->path,
            ));
        }

        return $this->contents;
    }
}
