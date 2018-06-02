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

/**
 * @internal
 */
final class ConfigHashNormalizerTest extends AbstractNormalizerTestCase
{
    public function testNormalizeDoesNotModifyOtherProperty(): void
    {
        $json = <<<'JSON'
{
  "foo": {
    "qux": "quux",
    "bar": "baz"
  }
}
JSON;

        $normalizer = new ConfigHashNormalizer();

        $this->assertSame($json, $normalizer->normalize($json));
    }

    /**
     * @dataProvider providerProperty
     *
     * @param string $property
     */
    public function testNormalizeIgnoresEmptyConfigHash(string $property): void
    {
        $json = <<<JSON
{
  "${property}": {}
}
JSON;

        $normalizer = new ConfigHashNormalizer();

        $this->assertSame($json, $normalizer->normalize($json));
    }

    /**
     * @dataProvider providerProperty
     *
     * @param string $property
     */
    public function testNormalizeSortsConfigHashIfPropertyExists(string $property): void
    {
        $json = <<<JSON
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
JSON;

        $normalized = <<<JSON
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
JSON;

        $normalizer = new ConfigHashNormalizer();

        $this->assertSame(\json_encode(\json_decode($normalized)), $normalizer->normalize($json));
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
        ];
    }
}
