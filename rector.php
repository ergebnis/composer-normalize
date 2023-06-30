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

use Rector\Config;
use Rector\Core;
use Rector\PHPUnit;

return static function (Config\RectorConfig $rectorConfig): void {
    $rectorConfig->cacheDirectory(__DIR__ . '/.build/rector/');

    $rectorConfig->import(__DIR__ . '/vendor/fakerphp/faker/rector-migrate.php');

    $rectorConfig->paths([
        __DIR__ . '/src/',
        __DIR__ . '/test/',
    ]);

    $rectorConfig->phpVersion(Core\ValueObject\PhpVersion::PHP_81);

    $rectorConfig->sets([
        PHPUnit\Set\PHPUnitSetList::PHPUNIT_90,
    ]);
};
