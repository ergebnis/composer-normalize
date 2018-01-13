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
* pass the content of `composer.json` through a chain of normalizers
* write the normalized content of `composer.json` back to the file
* update the hash in `composer.lock` if it exists and if an update is necessary

## Normalizers

This package makes use of the following normalizers provided by [`localheinz/json-normalizer`](https://github.com/localheinz/json-normalizer).

* [`Localheinz\Json\Normalizer\AutoFormatNormalizer`](https://github.com/localheinz/json-normalizer#autoformatnormalizer)
* [`Localheinz\Json\Normalizer\ChainNormalizer`](https://github.com/localheinz/json-normalizer#chainnormalizer)

Additionally, it provides and makes use of the following normalizers:

* [`Localheinz\Composer\Normalize\Normalizer\ConfigHashNormalizer`](#confighashnormalizer)

### `ConfigHashNormalizer`

If `composer.json` contains any configuration in the `config` section, 
the `ConfigHashNormalizer` will sort the `config` section by key in ascending order.

:bulb: Find out more about the `config` section at https://getcomposer.org/doc/06-config.md.  

## Contributing

Please have a look at [`CONTRIBUTING.md`](.github/CONTRIBUTING.md).

## Code of Conduct

Please have a look at [`CODE_OF_CONDUCT.md`](.github/CODE_OF_CONDUCT.md).

## License

This package is licensed using the MIT License.
