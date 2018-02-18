# composer-normalize

[![Build Status](https://travis-ci.org/localheinz/composer-normalize.svg?branch=master)](https://travis-ci.org/localheinz/composer-normalize)
[![codecov](https://codecov.io/gh/localheinz/composer-normalize/branch/master/graph/badge.svg)](https://codecov.io/gh/localheinz/composer-normalize)
[![Latest Stable Version](https://poser.pugx.org/localheinz/composer-normalize/v/stable)](https://packagist.org/packages/localheinz/composer-normalize)
[![Total Downloads](https://poser.pugx.org/localheinz/composer-normalize/downloads)](https://packagist.org/packages/localheinz/composer-normalize)

Provides a composer plugin for normalizing `composer.json`.

## Installation

Run

```
$ composer global require localheinz/composer-normalize
```

## Usage

Run

```
$ composer normalize
```

to normalize `composer.json` in the working directory.

The `NormalizeCommand` provided by the `NormalizePlugin` within this package will

* determine whether a `composer.json` exists
* determine whether a `composer.lock` exists, and if so, whether it is up to date
* use the `ComposerJsonNormalizer` to normalize the content of `composer.json`
* format the normalized content (either as sniffed, or as specified using the `--indent-size` and `--indent-style` options)
* write the normalized and formatted content of `composer.json` back to the file
* update the hash in `composer.lock` if it exists and if an update is necessary

### Arguments

* `file`: Path to composer.json file (optional, defaults to `composer.json` in working directory)

### Options

* `--dry-run`: Show the results of normalizing, but do not modify any files
* `--indent-size`: Indent size (an integer greater than 0); should be used with the `--indent-style` option
* `--indent-style`: Indent style (one of "space", "tab"); should be used with the `--indent-size` option
* `--no-update-lock`: Do not update lock file if it exists

## Normalizers

The `ComposerJsonNormalizer` composes normalizers provided by [`localheinz/json-normalizer`](https://github.com/localheinz/json-normalizer):

* [`Localheinz\Json\Normalizer\ChainNormalizer`](https://github.com/localheinz/json-normalizer#chainnormalizer)
* [`Localheinz\Json\Normalizer\SchemaNormalizer`](https://github.com/localheinz/json-normalizer#schemanormalizer)
 
as well as the following normalizers provided by this package:

* [`Localheinz\Composer\Normalize\Normalizer\BinNormalizer`](#binnormalizer)
* [`Localheinz\Composer\Normalize\Normalizer\ConfigHashNormalizer`](#confighashnormalizer)
* [`Localheinz\Composer\Normalize\Normalizer\PackageHashNormalizer`](#packagehashnormalizer)
* [`Localheinz\Composer\Normalize\Normalizer\VersionConstraintNormalizer`](#versionconstraintnormalizer)

### `BinNormalizer`

If `composer.json` contains an array of scripts in the `bin` section, 
the `BinNormalizer` will sort the elements of the `bin` section by value in ascending order.

:bulb: Find out more about the `bin` section at https://getcomposer.org/doc/04-schema.md#bin.
  
### `ConfigHashNormalizer`

If `composer.json` contains any configuration in the `config` section, 
the `ConfigHashNormalizer` will sort the `config` section by key in ascending order.

:bulb: Find out more about the `config` section at https://getcomposer.org/doc/06-config.md.  

### `PackageHashNormalizer`

If `composer.json` contains any configuration in the 

* `conflict`
* `provide`
* `replace`
* `require`
* `require-dev`
* `suggest`

sections, the `PackageHashNormalizer` will sort the content of these sections.

:bulb: This transfers the behaviour from using the `--sort-packages` or 
`sort-packages` configuration flag to other sections. Find out more about 
the `--sort-packages` flag and configuration at https://getcomposer.org/doc/06-config.md#sort-packages 
and https://getcomposer.org/doc/03-cli.md#require.

### `VersionConstraintNormalizer`

If `composer.json` contains version constraints in the  

* `conflict`
* `provide`
* `replace`
* `require`
* `require-dev`

sections, the `VersionConstraintNormalizer` will ensure that 

* all constraints are trimmed
* *and* constraints are separated by a single space (` `) or a comma (`,`)
* *or* constraints are separated by double-pipe with a single space before and after (` || `)
* *range* constraints are separated by a single space (` `)

:bulb: Find out more about version constraints at https://getcomposer.org/doc/articles/versions.md.

## Contributing

Please have a look at [`CONTRIBUTING.md`](.github/CONTRIBUTING.md).

## Code of Conduct

Please have a look at [`CODE_OF_CONDUCT.md`](.github/CODE_OF_CONDUCT.md).

## License

This package is licensed using the MIT License.

## Credits

The algorithm for sorting packages in the [`PackageHashNormalizer`](src/Normalizer/PackageHashNormalizer.php) has 
been adopted from [`Composer\Json\JsonManipulator::sortPackages()`](https://github.com/composer/composer/blob/1.6.2/src/Composer/Json/JsonManipulator.php#L110-L146) 
(originally licensed under MIT by [Nils Adermann](https://github.com/naderman) and [Jordi Boggiano](https://github.com/seldaek)), 
which I initially contributed to `composer/composer` with [`composer/composer#3549`](https://github.com/composer/composer/pull/3549)
and [`composer/composer#3872`](https://github.com/composer/composer/pull/3872).

## Services

`localheinz/composer-normalize` is currently in use by [FlintCI](https://flintci.io), see https://flintci.io/docs#composernormalize. 
