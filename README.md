# composer-normalize

[![Build Status](https://travis-ci.com/localheinz/composer-normalize.svg?branch=master)](https://travis-ci.com/localheinz/composer-normalize)
[![Build status](https://ci.appveyor.com/api/projects/status/94sp0o4bool7klcf/branch/master?svg=true)](https://ci.appveyor.com/project/localheinz/composer-normalize/branch/master)
[![codecov](https://codecov.io/gh/localheinz/composer-normalize/branch/master/graph/badge.svg)](https://codecov.io/gh/localheinz/composer-normalize)
[![Latest Stable Version](https://poser.pugx.org/localheinz/composer-normalize/v/stable)](https://packagist.org/packages/localheinz/composer-normalize)
[![Total Downloads](https://poser.pugx.org/localheinz/composer-normalize/downloads)](https://packagist.org/packages/localheinz/composer-normalize)

Provides a composer plugin for normalizing `composer.json`.

## Motivation

If you have been working with `composer` on more than one project, you might 
have noticed that each `composer.json` ends up being structured differently. 

I certainly have noticed, and rather than

* ignoring it
* manually structuring `composer.json`
* asking others to structure `composer.json`

I decided to build something that structures `composer.json` in an automated 
fashion, but without changing the initial intent.

In my opinion, the advantages of using `localheinz/composer-normalize` are

* no need to think (or argue) about where to add a new section
* no need to think (or argue) about proper formatting
* no need to worry about keeping items in a consistent order where they can't be kept in order by other means
* can be used in a Continuous Integration environment

:bulb: If you are interested in finding out more before giving it a try, I 
have written a blog post about [Normalizing composer.json](https://localheinz.com/blog/2018/01/15/normalizing-composer.json/).

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
* use the `ComposerJsonNormalizer` from [`localheinz/composer-json-normalizer`](https://github.com/localheinz/composer-json-normalizer) to normalize the content of `composer.json`
* format the normalized content (either as sniffed, or as specified using the `--indent-size` and `--indent-style` options)
* write the normalized and formatted content of `composer.json` back to the file
* update the hash in `composer.lock` if it exists and if an update is necessary

:bulb: Interested in what `ComposerJsonNormalizer` does? Head over to
[`localheinz/composer-json-normalizer`](https://github.com/localheinz/composer-json-normalizer#normalizers) for a full explanation.

### Arguments

* `file`: Path to composer.json file (optional, defaults to `composer.json` in working directory)

### Options

* `--dry-run`: Show the results of normalizing, but do not modify any files
* `--indent-size`: Indent size (an integer greater than 0); should be used with the `--indent-style` option
* `--indent-style`: Indent style (one of "space", "tab"); should be used with the `--indent-size` option
* `--no-update-lock`: Do not update lock file if it exists

## Changelog

Please have a look at [`CHANGELOG.md`](CHANGELOG.md).

## Contributing

Please have a look at [`CONTRIBUTING.md`](.github/CONTRIBUTING.md).

## Code of Conduct

Please have a look at [`CODE_OF_CONDUCT.md`](.github/CODE_OF_CONDUCT.md).

## License

This package is licensed using the MIT License.

## Services

`localheinz/composer-normalize` is currently in use by [FlintCI](https://flintci.io), see https://flintci.io/docs#composernormalize. 
