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

use Composer\Console\Application;
use Composer\Factory;
use Ergebnis\Composer\Normalize;
use Ergebnis\Json\Normalizer;
use Ergebnis\Json\Printer;
use Localheinz\Diff;
use Symfony\Component\Console;

require_once __DIR__ . '/../vendor/autoload.php';

$command = new Normalize\Command\NormalizeCommand(
    new Factory(),
    new Normalizer\Vendor\Composer\ComposerJsonNormalizer(__DIR__ . '/../resource/schema.json'),
    new Normalizer\Format\Formatter(new Printer\Printer()),
    new Diff\Differ(new Diff\Output\StrictUnifiedDiffOutputBuilder([
        'fromFile' => 'original',
        'toFile' => 'normalized',
    ]))
);

$application = new Application();

$application->add($command);

if (1 === \count($argv) || 'normalize' !== $argv[1]) {
    \array_splice(
        $argv,
        1,
        0,
        [
            'normalize',
        ]
    );
}

$application->run(new Console\Input\ArgvInput($argv));
