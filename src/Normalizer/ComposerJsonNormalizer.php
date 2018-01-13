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

use Localheinz\Json\Normalizer\AutoFormatNormalizer;
use Localheinz\Json\Normalizer\ChainNormalizer;
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
        $this->normalizer = new AutoFormatNormalizer(new ChainNormalizer(
            new SchemaNormalizer($schemaUri),
            new BinNormalizer(),
            new ConfigHashNormalizer(),
            new PackageHashNormalizer()
        ));
    }

    public function normalize(string $json): string
    {
        if (null === \json_decode($json) && JSON_ERROR_NONE !== \json_last_error()) {
            throw new \InvalidArgumentException(\sprintf(
                '"%s" is not valid JSON.',
                $json
            ));
        }

        return $this->normalizer->normalize($json);
    }
}
