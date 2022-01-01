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

final class State
{
    private Directory $directory;
    private File $composerJsonFile;
    private File $composerLockFile;
    private Directory $vendorDirectory;

    private function __construct(Directory $directory)
    {
        $this->directory = $directory;

        $this->composerJsonFile = File::fromPath(\sprintf(
            '%s/composer.json',
            $directory->path(),
        ));

        $this->composerLockFile = File::fromPath(\sprintf(
            '%s/composer.lock',
            $directory->path(),
        ));

        $this->vendorDirectory = Directory::fromPath(\sprintf(
            '%s/vendor',
            $directory->path(),
        ));
    }

    public static function fromDirectory(Directory $directory): self
    {
        return new self($directory);
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

    public function vendorDirectory(): Directory
    {
        return $this->vendorDirectory;
    }
}
