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

use Ergebnis\Rector;

$ruleSet = Rector\Config\RuleSet\Php74::create()->withRules(Rector\Config\Rules::fromArray([
    Rector\Rules\Files\ReferenceNamespacedSymbolsRelativeToNamespacePrefixRector::class => [
        'discoverNamespacePrefixes' => true,
        'parentNamespacePrefixes' => [
            'Ergebnis\Composer',
            'Symfony\Component',
        ],
    ],
]));

return Rector\Config\Factory::fromRuleSet($ruleSet)
    ->withCache(__DIR__ . '/.build/rector/')
    ->withPaths([
        __DIR__ . '/bin/',
        __DIR__ . '/src/',
        __DIR__ . '/test/',
        __DIR__ . '/.php-cs-fixer.php',
        __DIR__ . '/rector.php',
    ]);
