# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## `2.0`

### Unreleased

For a full diff see [`1.x...master`](https://github.com/localheinz/composer-normalize/compare/1.x...master).

#### Changed

* The constructor of `NormalizeCommand` now requires an implementation of `Localheinz\Json\Normalizer\Format\FormatterInterface`, as well as an instance of `Sebastian\Diff\Differ` to be injected ([#118](https://github.com/localheinz/composer-normalize/pull/118)), by [@localheinz](https://github.com/localheinz)

## `1.x`

### Unreleased

For a full diff see [`1.1.1...1.x`](https://github.com/localheinz/composer-normalize/compare/1.1.1...1.x).

### [`1.1.1`](https://github.com/localheinz/composer-normalize/releases/tag/1.1.1)

For a full diff see [`1.1.0...1.1.1`](https://github.com/localheinz/composer-normalize/compare/1.1.0...1.1.1).

#### Removed

* Updated [`localheinz/composer-json-normalizer`](http://github.com/localheinz/composer-json-normalizer), which effectively removed a dependency on [`composer/composer`](https://github.com/composer/composer) ([#157](https://github.com/localheinz/composer-normalize/pull/157)), by [@localheinz](https://github.com/localheinz)

### [`1.1.0`](https://github.com/localheinz/composer-normalize/releases/tag/1.1.0)

For a full diff see [`1.0.0...1.1.0`](https://github.com/localheinz/composer-normalize/compare/1.0.0...1.1.0).

#### Deprecated

* Deprecated the `file` argument of the `NormalizeCommand` as the same functionality can be achieved using the `--working-dir` option ([#145](https://github.com/localheinz/composer-normalize/pull/145)), by [@localheinz](https://github.com/localheinz)

#### Fixed

* Force reading `composer.json` and `composer.lock` after normalization to ensure `composer.lock` is updated when not fresh after normalization ([#139](https://github.com/localheinz/composer-normalize/pull/139)), by [@localheinz](https://github.com/localheinz)

### [`1.0.0`](https://github.com/localheinz/composer-normalize/releases/tag/1.0.0)

For a full diff see [`0.9.0...1.0.0`](https://github.com/localheinz/composer-normalize/compare/0.9.0...1.0.0).

#### Added

* Added this changelog ([#94](https://github.com/localheinz/composer-normalize/pull/94)), by [@localheinz](https://github.com/localheinz)

#### Removed

* Removed normalizers after extracting package [`localheinz/composer-json-normalizer`](https://github.com/localheinz/composer-json-normalizer) ([#106](https://github.com/localheinz/composer-normalize/pull/106)), by [@localheinz](https://github.com/localheinz)

### [`0.9.0`](https://github.com/localheinz/composer-normalize/releases/tag/0.9.0)

For a full diff see [`0.8.0...0.9.0`](https://github.com/localheinz/composer-normalize/compare/0.8.0...0.9.0).

#### Changed

* The `ConfigHashNormalizer` now also sorts the `scripts-descriptions` section ([#89](https://github.com/localheinz/composer-normalize/pull/89)), by [@localheinz](https://github.com/localheinz)

#### Fixed

* When validation of `composer.lock` fails prior to normalization, it is
  now recommended to update the lock file only ([#86](https://github.com/localheinz/composer-normalize/pull/86)), by [@svenluijten](https://github.com/svenluijten)

### [`0.8.0`](https://github.com/localheinz/composer-normalize/releases/tag/0.8.0)

For a full diff see [`0.7.0...0.8.0`](https://github.com/localheinz/composer-normalize/compare/0.7.0...0.8.0).

#### Changed

* The `ConfigHashNormalizer` now also sorts the `extra` section ([#60](https://github.com/localheinz/composer-normalize/pull/60)), by [@localheinz](https://github.com/localheinz)

### [`0.7.0`](https://github.com/localheinz/composer-normalize/releases/tag/0.7.0)

For a full diff see [`0.6.0...0.7.0`](https://github.com/localheinz/composer-normalize/compare/0.6.0...0.7.0).

#### Changed

* Updated `localheinz/json-normalizer`, which now sniffs the new-line
  character and uses it for printing instead of using `PHP_EOL` ([#62](https://github.com/localheinz/composer-normalize/pull/62)), by [@localheinz](https://github.com/localheinz)

### [`0.6.0`](https://github.com/localheinz/composer-normalize/releases/tag/0.6.0)

For a full diff see [`0.5.0...0.6.0`](https://github.com/localheinz/composer-normalize/compare/0.5.0...0.6.0).

#### Added

* Added a `file` argument to the `NormalizeCommand`, so the path to
  `composer.json` can be specified now, ([#51](https://github.com/localheinz/composer-normalize/pull/51)), by [@localheinz](https://github.com/localheinz)

### [`0.5.0`](https://github.com/localheinz/composer-normalize/releases/tag/0.5.0)

For a full diff see [`0.4.0...0.5.0`](https://github.com/localheinz/composer-normalize/compare/0.4.0...0.5.0).

#### Changed

* Updated `localheinz/json-normalizer`, which significantly improves the
  `SchemaNormalizer` employed to do the major normalization of
  `composer.json` ([#42](https://github.com/localheinz/composer-normalize/pull/42)), by [@localheinz](https://github.com/localheinz)

### [`0.4.0`](https://github.com/localheinz/composer-normalize/releases/tag/0.4.0)

For a full diff see [`0.3.0...0.4.0`](https://github.com/localheinz/composer-normalize/compare/0.3.0...0.4.0).

#### Added

* Added `--dry-run` option, which allows usage in Continuous Integration
  systems, as it renders a diff and exits with a non-zero exit code
  ([#38](https://github.com/localheinz/composer-normalize/pull/38)), by [@localheinz](https://github.com/localheinz)

### [`0.3.0`](https://github.com/localheinz/composer-normalize/releases/tag/0.3.0)

For a full diff see [`0.2.0...0.3.0`](https://github.com/localheinz/composer-normalize/compare/0.2.0...0.3.0).

#### Fixed

* Dropped support for PHP 7.0, which allows proper handling of empty
  PSR-4 namespace prefixes ([#30](https://github.com/localheinz/composer-normalize/pull/30)), by [@localheinz](https://github.com/localheinz)

### [`0.2.0`](https://github.com/localheinz/composer-normalize/releases/tag/0.2.0)

For a full diff see [`0.1.0...0.2.0`](https://github.com/localheinz/composer-normalize/compare/0.1.0...0.2.0).

#### Added

* Added `--no-update-lock` option, which allows skipping the update of
  `composer.lock` after normalization ([#28](https://github.com/localheinz/composer-normalize/pull/28)), by [@localheinz](https://github.com/localheinz)
* Added the `VersionConstraintNormalizer`, which normalizes version
  constraints ([#18](https://github.com/localheinz/composer-normalize/pull/18)), by [@localheinz](https://github.com/localheinz)

#### Fixed

* Using the `--no-scripts` option when invoking the `UpdateCommand` to
  update `composer.lock` ([#19](https://github.com/localheinz/composer-normalize/pull/19)), by [@localheinz](https://github.com/localheinz)

### [`0.1.0`](https://github.com/localheinz/composer-normalize/releases/tag/0.1.0)

For a full diff see [`81bc3a8...0.1.0`](https://github.com/localheinz/composer-normalize/compare/81bc3a8...0.1.0).

#### Added

* Added `NormalizeCommand` ([#1](https://github.com/localheinz/composer-normalize/pull/1)), by [@localheinz](https://github.com/localheinz)
* Added `ConfigHashNormalizer`, which sorts entries in the `config`
  section by key ([#2](https://github.com/localheinz/composer-normalize/pull/2)), by [@localheinz](https://github.com/localheinz)
* Added the `NormalizePlugin`, which provides the `NormalizeCommand`
  ([#3](https://github.com/localheinz/composer-normalize/pull/3)), by [@localheinz](https://github.com/localheinz)
* Added the `PackageHashNormalizer` which sorts packages in the
  `conflict`, `provide`, `replaces`, `require`, `require-dev`, and `suggest` sections
  using the same algorithm that is used by the `sort-packages` option of
  composer itself ([#6](https://github.com/localheinz/composer-normalize/pull/6)), by [@localheinz](https://github.com/localheinz)
* Added the `BinNormalizer`, which sorts entries in the `bin` section by
* Added the `ComposerJsonNormalizer`, which composes all of the above
  normalizers along with the `SchemaNormalizer`, to normalize
  `composer.json` according to its underlying JSON schema ([#8](https://github.com/localheinz/composer-normalize/pull/8) and [#10](https://github.com/localheinz/composer-normalize/pull/10)), by [@localheinz](https://github.com/localheinz)
