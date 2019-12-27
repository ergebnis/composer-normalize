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

use Composer\Factory;
use Ergebnis\Composer\Json;
use Ergebnis\Composer\Normalize;
use Ergebnis\Json\Normalizer;
use Ergebnis\Json\Printer;
use Localheinz\Diff;

require_once __DIR__ . '/../vendor/autoload.php';

$command = new Normalize\Command\NormalizeCommand(
    new Factory(),
    new Json\Normalizer\ComposerJsonNormalizer(__DIR__ . '/../resource/schema.json'),
    new Normalizer\Format\Formatter(new Printer\Printer()),
    new Diff\Differ(new Diff\Output\StrictUnifiedDiffOutputBuilder([
        'fromFile' => 'original',
        'toFile' => 'normalized',
    ]))
);

$application = new Normalize\Application();

$application->add($command);
$application->setDefaultCommand($command->getName());
$application->run();
