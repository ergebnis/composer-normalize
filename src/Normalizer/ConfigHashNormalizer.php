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

namespace Localheinz\Composer\Normalize\Normalizer;

use Localheinz\Json\Normalizer\Json;
use Localheinz\Json\Normalizer\NormalizerInterface;

final class ConfigHashNormalizer implements NormalizerInterface
{
    /**
     * @var string[]
     */
    private static $properties = [
        'config',
        'extra',
        'scripts-descriptions',
    ];

    public function normalize(Json $json): Json
    {
        $decoded = $json->decoded();

        if (!\is_object($decoded)) {
            return $json;
        }

        if (!\is_object($decoded)) {
            return $json;
        }

        $objectProperties = \array_intersect_key(
            \get_object_vars($decoded),
            \array_flip(self::$properties)
        );

        if (0 === \count($objectProperties)) {
            return $json;
        }

        foreach ($objectProperties as $name => $value) {
            $config = (array) $decoded->{$name};

            if (0 === \count($config)) {
                return $json;
            }

            \ksort($config);

            $decoded->{$name} = $config;
        }

        $encoded = \json_encode($decoded);

        return Json::fromEncoded($encoded);
    }
}
