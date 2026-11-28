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

namespace Ergebnis\Composer\Normalize\Test\DataProvider\Command;

use Ergebnis\Composer\Normalize;
use Ergebnis\Json\Normalizer\Format;

final class NormalizeCommandProvider
{
    use Normalize\Test\Util\Helper;

    /**
     * @return \Generator<string, array{0: Normalize\Test\Util\CommandInvocation}>
     */
    public static function commandInvocation(): \Generator
    {
        foreach (self::commandInvocations() as $commandInvocation) {
            yield $commandInvocation->style() => [
                $commandInvocation,
            ];
        }
    }

    /**
     * @return \Generator<string, array{0: Normalize\Test\Util\CommandInvocation, 1: string}>
     */
    public static function commandInvocationAndInvalidIndentSize(): \Generator
    {
        $faker = self::faker();

        $numberGreaterThanZero = $faker->numberBetween(1);

        /** @var array<string, string> $indentSizes */
        $indentSizes = [
            'string-arbitrary' => $faker->sentence(),
            'integer-zero-casted-to-string' => (string) 0,
            'integer-less-than-zero-casted-to-string' => (string) (-1 * $numberGreaterThanZero),
        ];

        foreach (self::commandInvocations() as $commandInvocation) {
            foreach ($indentSizes as $indentSizeKey => $indentSize) {
                $key = \sprintf(
                    '%s-indent-size-%s',
                    $commandInvocation->style(),
                    $indentSizeKey,
                );

                yield $key => [
                    $commandInvocation,
                    $indentSize,
                ];
            }
        }
    }

    /**
     * @return \Generator<string, array{0: Normalize\Test\Util\CommandInvocation, 1: int, 2: string}>
     */
    public static function commandInvocationIndentSizeAndIndentStyle(): \Generator
    {
        /** @var list<int> $indentSizes */
        $indentSizes = [
            1,
            self::faker()->numberBetween(2, 4),
        ];

        foreach (self::commandInvocations() as $commandInvocation) {
            foreach ($indentSizes as $indentSize) {
                foreach (\array_keys(Format\Indent::CHARACTERS) as $indentStyle) {
                    $key = \sprintf(
                        '%s-indent-size-%d-indent-style-%s',
                        $commandInvocation->style(),
                        (string) $indentSize,
                        $indentStyle,
                    );

                    yield $key => [
                        $commandInvocation,
                        $indentSize,
                        $indentStyle,
                    ];
                }
            }
        }
    }

    /**
     * @return array<int, Normalize\Test\Util\CommandInvocation>
     */
    private static function commandInvocations(): array
    {
        return [
            Normalize\Test\Util\CommandInvocation::inCurrentWorkingDirectory(),
            Normalize\Test\Util\CommandInvocation::usingFileArgument(),
            Normalize\Test\Util\CommandInvocation::usingWorkingDirectoryOption(),
        ];
    }
}
