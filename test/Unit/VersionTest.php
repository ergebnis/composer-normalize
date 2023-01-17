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

namespace Ergebnis\Composer\Normalize\Test\Unit;

use Ergebnis\Composer\Normalize\Version;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \Ergebnis\Composer\Normalize\Version
 */
final class VersionTest extends Framework\TestCase
{
    public function testLongReturnsVersion(): void
    {
        $expected = '<info>ergebnis/composer-normalize</info> by <info>Andreas Möller</info> and contributors';

        self::assertSame($expected, Version::long());
    }
}
