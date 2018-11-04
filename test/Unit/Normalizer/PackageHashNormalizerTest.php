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

use Localheinz\Composer\Normalize\Normalizer\PackageHashNormalizer;
use Localheinz\Json\Normalizer\Json;

/**
 * @internal
 */
final class PackageHashNormalizerTest extends AbstractNormalizerTestCase
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

        $normalizer = new PackageHashNormalizer();

        $normalized = $normalizer->normalize($json);

        $this->assertSame($json->encoded(), $normalized->encoded());
    }

    /**
     * @dataProvider providerProperty
     *
     * @param string $property
     */
    public function testNormalizeIgnoresEmptyPackageHash(string $property): void
    {
        $json = Json::fromEncoded(
<<<JSON
{
  "${property}": {}
}
JSON
        );

        $normalizer = new PackageHashNormalizer();

        $normalized = $normalizer->normalize($json);

        $this->assertSame(\json_encode(\json_decode($json->encoded())), $normalized->encoded());
    }

    /**
     * @dataProvider providerProperty
     *
     * @param string $property
     */
    public function testNormalizeSortsPackageHashIfPropertyExists(string $property): void
    {
        $json = Json::fromEncoded(
<<<JSON
{
  "${property}": {
    "localheinz/test-util": "Provides utilities for tests.",
    "hhvm": "Okay",
    "lib-baz": "Maybe it helps.",
    "localheinz/php-cs-fixer-config": "Provides a configuration factory and multiple rule sets for friendsofphp/php-cs-fixer.",
    "ext-foo": "Could be useful",
    "php": "Because why not, it's great."
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
    "php": "Because why not, it's great.",
    "hhvm": "Okay",
    "ext-foo": "Could be useful",
    "lib-baz": "Maybe it helps.",
    "localheinz/php-cs-fixer-config": "Provides a configuration factory and multiple rule sets for friendsofphp/php-cs-fixer.",
    "localheinz/test-util": "Provides utilities for tests."
  },
  "foo": {
    "qux": "quux",
    "bar": "baz"
  }
}
JSON
        );

        $normalizer = new PackageHashNormalizer();

        $normalized = $normalizer->normalize($json);

        $this->assertSame(\json_encode(\json_decode($expected->encoded())), $normalized->encoded());
    }

    public function providerProperty(): \Generator
    {
        foreach ($this->propertiesWhereKeysOfHashArePackages() as $value) {
            yield $value => [
                $value,
            ];
        }
    }

    private function propertiesWhereKeysOfHashArePackages(): array
    {
        return [
            'conflict',
            'provide',
            'replace',
            'require',
            'require-dev',
            'suggest',
        ];
    }
}
