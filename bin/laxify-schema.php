<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018-2023 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/composer-normalize
 */

$schemaFile = __DIR__ . '/../resource/schema.json';

$schema = \json_decode(
    \file_get_contents($schemaFile),
    false,
);

$schema->additionalProperties = true;
$schema->required = [];

\file_put_contents($schemaFile, \json_encode(
    $schema,
    \JSON_PRETTY_PRINT | \JSON_PRESERVE_ZERO_FRACTION | \JSON_UNESCAPED_SLASHES,
));
