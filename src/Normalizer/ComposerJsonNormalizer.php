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

final class ComposerJsonNormalizer implements Normalizer\NormalizerInterface
{
    /**
     * @var Normalizer\NormalizerInterface
     */
    private $normalizer;

    public function __construct(string $schemaUri = 'https://getcomposer.org/schema.json')
    {
        $this->normalizer = new Normalizer\ChainNormalizer(
            new Normalizer\SchemaNormalizer($schemaUri),
            new BinNormalizer(),
            new ConfigHashNormalizer(),
            new PackageHashNormalizer(),
            new VersionConstraintNormalizer()
        );
    }

    public function normalize(string $json): string
    {
        $decoded = \json_decode($json);

        if (null === $decoded && \JSON_ERROR_NONE !== \json_last_error()) {
            throw new \InvalidArgumentException(\sprintf(
                '"%s" is not valid JSON.',
                $json
            ));
        }

        if (!\is_object($decoded)) {
            return $json;
        }

        return $this->normalizer->normalize($json);
    }
}
