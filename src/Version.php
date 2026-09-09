<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018-2026 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/composer-normalize
 */

namespace Ergebnis\Composer\Normalize;

use Composer\InstalledVersions;

/**
 * @internal
 */
final class Version
{
    private const NAME = 'ergebnis/composer-normalize';
    private const ATTRIBUTION = 'by <info>Andreas Möller</info> and contributors';

    /**
     * @see https://github.com/box-project/box/blob/master/doc/configuration.md#pretty-git-tag-placeholder-git
     */
    private static string $version = '@git@';

    public static function long(): string
    {
        $version = self::version();

        if ('' === $version) {
            return \sprintf(
                '<info>%s</info> %s',
                self::NAME,
                self::ATTRIBUTION,
            );
        }

        return \sprintf(
            '<info>%s</info> %s %s',
            self::NAME,
            $version,
            self::ATTRIBUTION,
        );
    }

    private static function version(): string
    {
        if ('@' . 'git@' !== self::$version) {
            return self::$version;
        }

        if (!InstalledVersions::isInstalled(self::NAME)) {
            return '';
        }

        $version = InstalledVersions::getPrettyVersion(self::NAME);

        if (!\is_string($version)) {
            return '';
        }

        return $version;
    }
}
