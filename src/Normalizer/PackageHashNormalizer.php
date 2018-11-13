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

use Composer\Repository;
use Localheinz\Json\Normalizer\Json;
use Localheinz\Json\Normalizer\NormalizerInterface;

final class PackageHashNormalizer implements NormalizerInterface
{
    /**
     * @var string[]
     */
    private static $properties = [
        'conflict',
        'provide',
        'replace',
        'require',
        'require-dev',
        'suggest',
    ];

    public function normalize(Json $json): Json
    {
        $decoded = $json->decoded();

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
            $packages = (array) $decoded->{$name};

            if (0 === \count($packages)) {
                continue;
            }

            $decoded->{$name} = $this->sortPackages($packages);
        }

        /** @var string $encoded */
        $encoded = \json_encode($decoded);

        return Json::fromEncoded($encoded);
    }

    /**
     * This code is adopted from composer/composer (originally licensed under MIT by Nils Adermann <naderman@naderman.de>
     * and Jordi Boggiano <j.boggiano@seld.be>).
     *
     * @see https://github.com/composer/composer/blob/1.6.2/src/Composer/Json/JsonManipulator.php#L110-L146
     *
     * @param string[] $packages
     *
     * @return string[]
     */
    private function sortPackages(array $packages): array
    {
        $prefix = function ($requirement) {
            if (1 === \preg_match(Repository\PlatformRepository::PLATFORM_PACKAGE_REGEX, $requirement)) {
                return \preg_replace(
                    [
                        '/^php/',
                        '/^hhvm/',
                        '/^ext/',
                        '/^lib/',
                        '/^\D/',
                    ],
                    [
                        '0-$0',
                        '1-$0',
                        '2-$0',
                        '3-$0',
                        '4-$0',
                    ],
                    $requirement
                );
            }

            return '5-' . $requirement;
        };

        \uksort($packages, function ($a, $b) use ($prefix) {
            return \strnatcmp($prefix($a), $prefix($b));
        });

        return $packages;
    }
}
