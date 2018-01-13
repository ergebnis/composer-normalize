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

final class BinNormalizerTest extends AbstractNormalizerTestCase
{
    public function testNormalizeDoesNotModifyOtherProperty()
    {
        $json = <<<'JSON'
{
  "foo": {
    "qux": "quux",
    "bar": "baz"
  }
}
JSON;

        $normalizer = new BinNormalizer();

        $this->assertSame($json, $normalizer->normalize($json));
    }

    public function testNormalizeDoesNotModifyBinIfPropertyExistsAsString()
    {
        $json = <<<'JSON'
{
  "bin": "foo.php",
  "foo": {
    "qux": "quux",
    "bar": "baz"
  }
}
JSON;

        $normalizer = new BinNormalizer();

        $this->assertSame($json, $normalizer->normalize($json));
    }

    public function testNormalizeSortsBinIfPropertyExistsAsArray()
    {
        $json = <<<'JSON'
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
JSON;

        $normalized = <<<'JSON'
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
JSON;

        $normalizer = new BinNormalizer();

        $this->assertSame(\json_encode(\json_decode($normalized)), $normalizer->normalize($json));
    }
}
