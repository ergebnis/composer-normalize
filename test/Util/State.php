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

final class State
{
    /**
     * @var Directory
     */
    private $directory;

    /**
     * @var File
     */
    private $composerJsonFile;

    /**
     * @var File
     */
    private $composerLockFile;

    /**
     * @var Directory
     */
    private $vendorDirectory;

    private function __construct()
    {
    }

    public static function fromDirectory(Directory $directory): self
    {
        $state = new self();

        $state->directory = $directory;

        $state->composerJsonFile = File::fromPath(\sprintf(
            '%s/composer.json',
            $directory->path()
        ));

        $state->composerLockFile = File::fromPath(\sprintf(
            '%s/composer.lock',
            $directory->path()
        ));

        $state->vendorDirectory = Directory::fromPath(\sprintf(
            '%s/vendor',
            $directory->path()
        ));

        return $state;
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
