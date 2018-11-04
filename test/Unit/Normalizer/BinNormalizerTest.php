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

use Localheinz\Composer\Normalize\Normalizer\BinNormalizer;
use Localheinz\Json\Normalizer\Json;

/**
 * @internal
 */
final class BinNormalizerTest extends AbstractNormalizerTestCase
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

        $normalizer = new BinNormalizer();

        $normalized = $normalizer->normalize($json);

        $this->assertSame($json->encoded(), $normalized->encoded());
    }

    public function testNormalizeDoesNotModifyBinIfPropertyExistsAsString(): void
    {
        $json = Json::fromEncoded(
<<<'JSON'
{
  "bin": "foo.php",
  "foo": {
    "qux": "quux",
    "bar": "baz"
  }
}
JSON
        );

        $normalizer = new BinNormalizer();

        $normalized = $normalizer->normalize($json);

        $this->assertSame($json->encoded(), $normalized->encoded());
    }

    public function testNormalizeSortsBinIfPropertyExistsAsArray(): void
    {
        $json = Json::fromEncoded(
<<<'JSON'
{
  "bin": [
    "script.php",
    "another-script.php"
  ],
  "foo": {
    "qux": "quux",
    "bar": "baz"
  }  
}
JSON
        );

        $expected = Json::fromEncoded(
<<<'JSON'
{
  "bin": [
    "another-script.php",
    "script.php"
  ],
  "foo": {
    "qux": "quux",
    "bar": "baz"
  }
}
JSON
        );

        $normalizer = new BinNormalizer();

        $normalized = $normalizer->normalize($json);

        $this->assertSame(\json_encode(\json_decode($expected->encoded())), $normalized->encoded());
    }
}
