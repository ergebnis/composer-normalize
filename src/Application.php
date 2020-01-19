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

namespace Ergebnis\Composer\Normalize;

use Composer\Console;

/**
 * @internal
 */
final class Application extends Console\Application
{
    public function getLongVersion(): string
    {
        return \sprintf(
            '%s <info>%s</info> with ergebnis/composer-normalize <info>@git@</info>',
            $this->getName(),
            $this->getVersion()
        );
    }
}
