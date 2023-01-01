<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018-2023 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/composer-normalize
 */

namespace Ergebnis\Composer\Normalize\Test\Util;

final class State
{
    private function __construct(
        private Directory $directory,
        private File $composerJsonFile,
        private File $composerLockFile,
    ) {
    }

    public static function fromDirectory(Directory $directory): self
    {
        return new self(
            $directory,
            File::fromPath(\sprintf(
                '%s/composer.json',
                $directory->path(),
            )),
            File::fromPath(\sprintf(
                '%s/composer.lock',
                $directory->path(),
            )),
        );
    }

    public function directory(): Directory
    {
        return $this->directory;
    }

    public function composerJsonFile(): File
    {
        return $this->composerJsonFile;
    }

    public function composerLockFile(): File
    {
        return $this->composerLockFile;
    }
}
