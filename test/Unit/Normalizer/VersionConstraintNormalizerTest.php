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

use Localheinz\Composer\Normalize\Normalizer\VersionConstraintNormalizer;
use Localheinz\Json\Normalizer\Json;

/**
 * @internal
 */
final class VersionConstraintNormalizerTest extends AbstractNormalizerTestCase
{
    /**
     * @dataProvider providerVersionConstraint
     *
     * @param string $constraint
     */
    public function testNormalizeDoesNotModifyOtherProperty(string $constraint): void
    {
        $json = Json::fromEncoded(
<<<JSON
{
  "foo": {
    "bar/baz": "${constraint}"
  }
}
JSON
        );

        $normalizer = new VersionConstraintNormalizer();

        $normalized = $normalizer->normalize($json);

        $this->assertSame($json->encoded(), $normalized->encoded());
    }

    public function providerVersionConstraint(): \Generator
    {
        foreach (\array_keys($this->versionConstraints()) as $versionConstraint) {
            yield [
                $versionConstraint,
            ];
        }
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

        $normalizer = new VersionConstraintNormalizer();

        $normalized = $normalizer->normalize($json);

        $this->assertSame(\json_encode(\json_decode($json->encoded())), $normalized->encoded());
    }

    public function providerProperty(): \Generator
    {
        $properties = $this->propertiesWhereValuesOfHashAreVersionConstraints();

        foreach ($properties as $property) {
            yield [
                $property,
            ];
        }
    }

    /**
     * @dataProvider providerPropertyAndVersionConstraint
     *
     * @param string $property
     * @param string $versionConstraint
     * @param string $normalizedVersionConstraint
     */
    public function testNormalizeNormalizesVersionConstraints(string $property, string $versionConstraint, string $normalizedVersionConstraint): void
    {
        $json = Json::fromEncoded(
<<<JSON
{
  "${property}": {
    "bar/baz": "${versionConstraint}"
  }
}
JSON
        );

        $expected = Json::fromEncoded(
<<<JSON
{
  "${property}": {
    "bar/baz": "${normalizedVersionConstraint}"
  }
}
JSON
        );

        $normalizer = new VersionConstraintNormalizer();

        $normalized = $normalizer->normalize($json);

        $this->assertJsonStringEqualsJsonString($expected->encoded(), $normalized->encoded());
    }

    public function providerPropertyAndVersionConstraint(): \Generator
    {
        $properties = $this->propertiesWhereValuesOfHashAreVersionConstraints();
        $versionConstraints = $this->versionConstraints();

        foreach ($properties as $property) {
            foreach ($versionConstraints as $versionConstraint => $normalizedVersionConstraint) {
                yield [
                    $property,
                    $versionConstraint,
                    $normalizedVersionConstraint,
                ];
            }
        }
    }

    /**
     * @dataProvider providerPropertyAndUntrimmedVersionConstraint
     *
     * @param string $property
     * @param string $versionConstraint
     * @param string $trimmedVersionConstraint
     */
    public function testNormalizeNormalizesTrimsVersionConstraints(string $property, string $versionConstraint, string $trimmedVersionConstraint): void
    {
        $json = Json::fromEncoded(
<<<JSON
{
  "${property}": {
    "bar/baz": "${versionConstraint}"
  }
}
JSON
        );

        $expected = Json::fromEncoded(
<<<JSON
{
  "${property}": {
    "bar/baz": "${trimmedVersionConstraint}"
  }
}
JSON
        );

        $normalizer = new VersionConstraintNormalizer();

        $normalized = $normalizer->normalize($json);

        $this->assertJsonStringEqualsJsonString($expected->encoded(), $normalized->encoded());
    }

    public function providerPropertyAndUntrimmedVersionConstraint(): \Generator
    {
        $spaces = [
            '',
            ' ',
        ];

        $properties = $this->propertiesWhereValuesOfHashAreVersionConstraints();
        $versionConstraints = \array_unique(\array_values($this->versionConstraints()));

        foreach ($properties as $property) {
            foreach ($versionConstraints as $trimmedVersionConstraint) {
                foreach ($spaces as $prefix) {
                    foreach ($spaces as $suffix) {
                        $untrimmedVersionConstraint = $prefix . $trimmedVersionConstraint . $suffix;

                        if ($trimmedVersionConstraint === $untrimmedVersionConstraint) {
                            continue;
                        }

                        yield [
                            $property,
                            $untrimmedVersionConstraint,
                            $trimmedVersionConstraint,
                        ];
                    }
                }
            }
        }
    }

    private function propertiesWhereValuesOfHashAreVersionConstraints(): array
    {
        return [
            'conflict',
            'provide',
            'replace',
            'require',
            'require-dev',
        ];
    }

    /**
     * @see https://getcomposer.org/doc/articles/versions.md
     *
     * @return array
     */
    private function versionConstraints(): array
    {
        return [
            /**
             * @see https://getcomposer.org/doc/articles/versions.md#branches
             */
            'dev-master' => 'dev-master',
            'dev-my-feature' => 'dev-my-feature',
            'dev-master#bf2eeff' => 'dev-master#bf2eeff',
            /**
             * @see https://getcomposer.org/doc/articles/versions.md#exact-version-constraint
             */
            '1.0.2' => '1.0.2',
            /**
             * @see https://getcomposer.org/doc/articles/versions.md#version-range
             */
            '>=1.0' => '>=1.0',
            '>=1.0 <2.0' => '>=1.0 <2.0',
            '>=1.0,<2.0' => '>=1.0,<2.0',
            '>=1.0  <2.0' => '>=1.0 <2.0',
            '>=1.0 , <2.0' => '>=1.0,<2.0',
            '>=1.0 <1.1 || >=1.2' => '>=1.0 <1.1 || >=1.2',
            '>=1.0,<1.1 || >=1.2' => '>=1.0,<1.1 || >=1.2',
            '>=1.0  <1.1||>=1.2' => '>=1.0 <1.1 || >=1.2',
            /**
             * @see https://getcomposer.org/doc/articles/versions.md#hyphenated-version-range-
             */
            '1.0 - 2.0' => '1.0 - 2.0',
            '1.0  -  2.0' => '1.0 - 2.0',
            /**
             * @see https://getcomposer.org/doc/articles/versions.md#next-significant-release-operators
             */
            '~1.2' => '~1.2',
            /**
             * @see https://getcomposer.org/doc/articles/versions.md#caret-version-range-
             */
            '^1.2.3' => '^1.2.3',
        ];
    }
}
