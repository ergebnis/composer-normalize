<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/localheinz/composer-normalize
 */

namespace Localheinz\Composer\Normalize\Command;

use Composer\Json;

final class SchemaUriResolver
{
    public static function resolve(): string
    {
        $remoteSchemaUri = 'https://getcomposer.org/schema.json';

        try {
            $reflection = new \ReflectionClass(Json\JsonFile::class);
        } catch (\ReflectionException $exception) {
            return $remoteSchemaUri;
        }

        $fileName = $reflection->getFileName();

        if (!\is_string($fileName)) {
            return $remoteSchemaUri;
        }

        $localSchemaUri = \sprintf(
            '%s/../../data/Composer/res/composer-schema.json',
            \dirname($fileName)
        );

        if (1 !== \preg_match('@://@', $localSchemaUri)) {
            $localSchemaUri = \sprintf(
                'file://%s',
                $localSchemaUri
            );
        }

        return $localSchemaUri;
    }
}
