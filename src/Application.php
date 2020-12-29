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
    /**
     * @see https://github.com/box-project/box/blob/master/doc/configuration.md#pretty-git-tag-placeholder-git
     *
     * @var string
     */
    private $version = '@git@';

    public function getLongVersion(): string
    {
        $attribution = 'by <info>Andreas Möller</info> and contributors';

        $version = $this->getVersion();

        if ('' === $version) {
            return \sprintf(
                '<info>%s</info> %s',
                $this->getName(),
                $attribution
            );
        }

        return \sprintf(
            '<info>%s</info> %s %s',
            $this->getName(),
            $version,
            $attribution
        );
    }

    public function getName(): string
    {
        return 'ergebnis/composer-normalize';
    }

    public function getVersion(): string
    {
        if ('@' . 'git@' === $this->version) {
            return '';
        }

        return $this->version;
    }
}
