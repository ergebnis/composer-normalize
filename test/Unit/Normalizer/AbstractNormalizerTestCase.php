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
use Localheinz\PHPUnit\Framework\Constraint\Provider;
use Localheinz\Test\Util\Helper;
use PHPUnit\Framework;

/**
 * @internal
 */
abstract class AbstractNormalizerTestCase extends Framework\TestCase
{
    use Helper;
    use Provider;

    final public function testImplementsNormalizerInterface(): void
    {
        $this->assertClassImplementsInterface(Normalizer\NormalizerInterface::class, $this->className());
    }

    final public function testNormalizeRejectsInvalidJson(): void
    {
        $json = $this->faker()->realText();

        $reflection = new \ReflectionClass($this->className());

        $normalizer = $reflection->newInstanceWithoutConstructor();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(\sprintf(
            '"%s" is not valid JSON.',
            $json
        ));

        $normalizer->normalize($json);
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
