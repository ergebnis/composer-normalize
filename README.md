# composer-normalize

[![Build Status](https://travis-ci.org/localheinz/composer-normalize.svg?branch=master)](https://travis-ci.org/localheinz/composer-normalize)
[![codecov](https://codecov.io/gh/localheinz/composer-normalize/branch/master/graph/badge.svg)](https://codecov.io/gh/localheinz/composer-normalize)
[![Latest Stable Version](https://poser.pugx.org/localheinz/composer-normalize/v/stable)](https://packagist.org/packages/localheinz/composer-normalize)
[![Total Downloads](https://poser.pugx.org/localheinz/composer-normalize/downloads)](https://packagist.org/packages/localheinz/composer-normalize)

## Installation

Run

```
$ composer global require localheinz/composer-normalize
```

## Usage

This package comes with the following normalizers:

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
