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

use Localheinz\Json\Normalizer;
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
        $this->assertClassImplementsInterface(Normalizer\NormalizerInterface::class, $this->className());
    }

    final public function testNormalizeRejectsInvalidJson(): void
    {
        $json = $this->faker()->realText();

        $normalizer = $this->createNormalizer();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(\sprintf(
            '"%s" is not valid JSON.',
            $json
        ));

        $normalizer->normalize($json);
    }

    /**
     * @dataProvider providerJsonNotDecodingToObject
     *
     * @param string $json
     */
    final public function testNormalizeDoesNotModifyWhenJsonDecodedIsNotAnObject(string $json): void
    {
        $normalizer = $this->createNormalizer();

        $normalized = $normalizer->normalize($json);

        $this->assertSame($json, $normalized);
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

    private function createNormalizer(): Normalizer\NormalizerInterface
    {
        $reflection = new \ReflectionClass($this->className());

        return $reflection->newInstanceWithoutConstructor();
    }
}
