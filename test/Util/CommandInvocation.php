<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018-2022 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/composer-normalize
 */

namespace Ergebnis\Composer\Normalize\Test\Util;

final class CommandInvocation
{
    private string $style;

    private function __construct(string $style)
    {
        $this->style = $style;
    }

    public static function inCurrentWorkingDirectory(): self
    {
        return new self('in-current-working-directory');
    }

    public static function usingFileArgument(): self
    {
        return new self('using-file-argument');
    }

    public static function usingWorkingDirectoryOption(): self
    {
        return new self('using-working-directory-option');
    }

    public function style(): string
    {
        return $this->style;
    }

    public function is(self $other): bool
    {
        return $this->style === $other->style;
    }
}
