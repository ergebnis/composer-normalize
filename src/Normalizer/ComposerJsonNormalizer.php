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

use Localheinz\Json\Normalizer\ChainNormalizer;
use Localheinz\Json\Normalizer\Json;
use Localheinz\Json\Normalizer\NormalizerInterface;
use Localheinz\Json\Normalizer\SchemaNormalizer;

final class ComposerJsonNormalizer implements NormalizerInterface
{
    /**
     * @var NormalizerInterface
     */
    private $normalizer;

    public function __construct(string $schemaUri = 'https://getcomposer.org/schema.json')
    {
        $this->normalizer = new ChainNormalizer(
            new SchemaNormalizer($schemaUri),
            new BinNormalizer(),
            new ConfigHashNormalizer(),
            new PackageHashNormalizer(),
            new VersionConstraintNormalizer()
        );
    }

    public function normalize(Json $json): Json
    {
        if (!\is_object($json->decoded())) {
            return $json;
        }

        return $this->normalizer->normalize($json);
    }
}
