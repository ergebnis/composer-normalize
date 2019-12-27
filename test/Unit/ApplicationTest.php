<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
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
        $composerApplication = new \Composer\Console\Application();
        $application = new Application();

        $expected = \sprintf(
            '%s <info>%s</info> with ergebnis/composer-normalize <info>@git@</info>',
            $composerApplication->getName(),
            $composerApplication->getVersion()
        );

        self::assertSame($expected, $application->getLongVersion());
    }
}
