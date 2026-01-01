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

use Ergebnis\License;
use Ergebnis\PhpCsFixer;
use PhpCsFixer\Finder;

$license = License\Type\MIT::markdown(
    __DIR__ . '/LICENSE.md',
    License\Range::since(
        License\Year::fromString('2018'),
        new DateTimeZone('UTC'),
    ),
    License\Holder::fromString('Andreas Möller'),
    License\Url::fromString('https://github.com/ergebnis/composer-normalize'),
);

$license->save();

$ruleSet = PhpCsFixer\Config\RuleSet\Php74::create()
    ->withHeader($license->header())
    ->withRules(PhpCsFixer\Config\Rules::fromArray([
        'no_useless_concat_operator' => false,
    ]));

$finder = Finder::create()
    ->exclude([
        '.build/',
        '.github/',
        '.note/',
    ])
    ->ignoreDotFiles(false)
    ->in(__DIR__);

$config = PhpCsFixer\Config\Factory::fromRuleSet($ruleSet);

$config->setCacheFile(__DIR__ . '/.build/php-cs-fixer/.php-cs-fixer.cache');
$config->setFinder($finder);

return $config;
