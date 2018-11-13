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

final class BinNormalizer implements NormalizerInterface
{
    public function normalize(Json $json): Json
    {
        $decoded = $json->decoded();

        if (!\is_object($decoded)
            || !\property_exists($decoded, 'bin')
            || !\is_array($decoded->bin)
        ) {
            return $json;
        }

        $bin = $decoded->bin;

        \sort($bin);

        $decoded->bin = $bin;

        /** @var string $encoded */
        $encoded = \json_encode($decoded);

        return Json::fromEncoded($encoded);
    }
}
