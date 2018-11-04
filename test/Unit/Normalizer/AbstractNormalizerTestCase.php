<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018 Andreas Möller.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/localheinz/composer-normalize
 */

namespace Localheinz\Composer\Normalize\Test\Unit\Normalizer;

use Localheinz\Json\Normalizer\Json;
use Localheinz\Json\Normalizer\NormalizerInterface;
use Localheinz\Test\Util\Helper;
use PHPUnit\Framework;

/**
 * @internal
 */
abstract class AbstractNormalizerTestCase extends Framework\TestCase
{
    use Helper;

    final public function testImplementsNormalizerInterface(): void
    {
        $this->assertClassImplementsInterface(NormalizerInterface::class, $this->className());
    }

    /**
     * @dataProvider providerJsonNotDecodingToObject
     *
     * @param string $encoded
     */
    final public function testNormalizeDoesNotModifyWhenJsonDecodedIsNotAnObject(string $encoded): void
    {
        $json = Json::fromEncoded($encoded);

        $reflection = new \ReflectionClass($this->className());

        /** @var NormalizerInterface $normalizer */
        $normalizer = $reflection->newInstanceWithoutConstructor();

        $normalized = $normalizer->normalize($json);

        $this->assertSame($json->encoded(), $normalized->encoded());
    }

    public function providerJsonNotDecodingToObject(): \Generator
    {
        $faker = $this->faker();

        $values = [
            'array' => $faker->words,
            'bool-false' => false,
            'bool-true' => true,
            'float' => $faker->randomFloat(),
            'int' => $faker->randomNumber(),
            'null' => null,
            'string' => $faker->sentence,
        ];

        foreach ($values as $key => $value) {
            yield $key => [
                \json_encode($value),
            ];
        }
    }

    final protected function className(): string
    {
        return \preg_replace(
            '/Test$/',
            '',
            \str_replace(
                'Localheinz\\Composer\\Normalize\\Test\\Unit\\',
                'Localheinz\\Composer\\Normalize\\',
                static::class
            )
        );
    }
}
