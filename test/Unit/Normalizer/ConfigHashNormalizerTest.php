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

use Localheinz\Composer\Normalize\Normalizer\ConfigHashNormalizer;
use Localheinz\Json\Normalizer\Json;

/**
 * @internal
 */
final class ConfigHashNormalizerTest extends AbstractNormalizerTestCase
{
    public function testNormalizeDoesNotModifyOtherProperty(): void
    {
        $json = Json::fromEncoded(
<<<'JSON'
{
  "foo": {
    "qux": "quux",
    "bar": "baz"
  }
}
JSON
        );

        $normalizer = new ConfigHashNormalizer();

        $normalized = $normalizer->normalize($json);

        $this->assertSame($json->encoded(), $normalized->encoded());
    }

    /**
     * @dataProvider providerProperty
     *
     * @param string $property
     */
    public function testNormalizeIgnoresEmptyConfigHash(string $property): void
    {
        $json = Json::fromEncoded(
<<<JSON
{
  "${property}": {}
}
JSON
        );

        $normalizer = new ConfigHashNormalizer();

        $normalized = $normalizer->normalize($json);

        $this->assertSame($json->encoded(), $normalized->encoded());
    }

    /**
     * @dataProvider providerProperty
     *
     * @param string $property
     */
    public function testNormalizeSortsConfigHashIfPropertyExists(string $property): void
    {
        $json = Json::fromEncoded(
<<<JSON
{
  "${property}": {
    "sort-packages": true,
    "preferred-install": "dist"
  },
  "foo": {
    "qux": "quux",
    "bar": "baz"
  }  
}
JSON
        );

        $expected = Json::fromEncoded(
<<<JSON
{
  "${property}": {
    "preferred-install": "dist",
    "sort-packages": true
  },
  "foo": {
    "qux": "quux",
    "bar": "baz"
  }
}
JSON
        );

        $normalizer = new ConfigHashNormalizer();

        $normalized = $normalizer->normalize($json);

        $this->assertSame(\json_encode(\json_decode($expected->encoded())), $normalized->encoded());
    }

    public function providerProperty(): \Generator
    {
        foreach ($this->properties() as $value) {
            yield $value => [
                $value,
            ];
        }
    }

    private function properties(): array
    {
        return [
            'config',
            'extra',
            'scripts-descriptions',
        ];
    }
}
