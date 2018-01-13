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

use Localheinz\Json\Normalizer\NormalizerInterface;

final class ConfigHashNormalizer implements NormalizerInterface
{
    public function normalize(string $json): string
    {
        $decoded = \json_decode($json);

        if (null === $decoded && JSON_ERROR_NONE !== \json_last_error()) {
            throw new \InvalidArgumentException(\sprintf(
                '"%s" is not valid JSON.',
                $json
            ));
        }

        if (!\property_exists($decoded, 'config')) {
            return $json;
        }

        $config = (array) $decoded->config;

        \ksort($config);

        $decoded->config = $config;

        return \json_encode($decoded);
    }
}
