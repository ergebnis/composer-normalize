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

use Localheinz\Json\Normalizer;

final class BinNormalizer implements Normalizer\NormalizerInterface
{
    public function normalize(string $json): string
    {
        $decoded = \json_decode($json);

        if (null === $decoded && \JSON_ERROR_NONE !== \json_last_error()) {
            throw new \InvalidArgumentException(\sprintf(
                '"%s" is not valid JSON.',
                $json
            ));
        }

        if (!\is_object($decoded)
            || !\property_exists($decoded, 'bin')
            || !\is_array($decoded->bin)
        ) {
            return $json;
        }

        $bin = (array) $decoded->bin;

        \sort($bin);

        $decoded->bin = $bin;

        return \json_encode($decoded);
    }
}
