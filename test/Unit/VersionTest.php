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

namespace Ergebnis\Composer\Normalize\Test\Unit;

use Composer\InstalledVersions;
use Ergebnis\Composer\Normalize\Test;
use Ergebnis\Composer\Normalize\Version;
use PHPUnit\Framework;

/**
 * @covers \Ergebnis\Composer\Normalize\Version
 */
final class VersionTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testLongReturnsVersionWhenPlaceholderHasBeenReplaced(): void
    {
        $version = self::faker()->semver();

        $expected = \sprintf(
            '<info>ergebnis/composer-normalize</info> %s by <info>Andreas Möller</info> and contributors',
            $version,
        );

        self::assertSame($expected, self::longWithVersion($version));
    }

    public function testLongReturnsVersionWhenPlaceholderHasNotBeenReplaced(): void
    {
        $expected = \sprintf(
            '<info>ergebnis/composer-normalize</info> %s by <info>Andreas Möller</info> and contributors',
            InstalledVersions::getPrettyVersion('ergebnis/composer-normalize'),
        );

        self::assertSame($expected, Version::long());
    }

    /**
     * Simulates a phar, where box has replaced the version placeholder with the version of
     * the git tag the phar has been built from.
     */
    private static function longWithVersion(string $version): string
    {
        $property = new \ReflectionProperty(
            Version::class,
            'version',
        );

        $property->setAccessible(true);

        $placeholder = $property->getValue();

        $property->setValue(
            null,
            $version,
        );

        try {
            return Version::long();
        } finally {
            $property->setValue(
                null,
                $placeholder,
            );
        }
    }
}
