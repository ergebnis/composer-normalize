<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018-2020 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/composer-normalize
 */

namespace Ergebnis\Composer\Normalize\Test\Unit;

use Ergebnis\Composer\Normalize\Application;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \Ergebnis\Composer\Normalize\Application
 */
final class ApplicationTest extends Framework\TestCase
{
    public function testGetLongVersionReturnsVersion(): void
    {
        $application = new Application();

        $expected = '<info>ergebnis/composer-normalize</info> by <info>Andreas Möller</info> and contributors';

        self::assertSame($expected, $application->getLongVersion());
    }
}
