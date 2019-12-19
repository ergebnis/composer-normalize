# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## Unreleased

For a full diff see [`2.0.2...master`][2.0.2...master]

## [`2.0.2`][2.0.2]

For a full diff see [`2.0.1...2.0.2`][2.0.1...2.0.2]

### Fixed

* Brought back support for PHP 7.1 ([#280]), by [@localheinz]

## [`2.0.1`][2.0.1]

For a full diff see [`2.0.0...2.0.1`][2.0.0...2.0.1]

## Changed

* Removed `Ergebnis\Composer\Normalize\Command\SchemaUriResolver` and checked in `schema.json` instead ([#273]), by [@localheinz]

## [`2.0.0`][2.0.0]

For a full diff see [`1.3.1...2.0.0`][1.3.1...2.0.0]

## Changed

* Started using `ergebnis/composer-json-normalizer` instead of `localheinz/composer-json-normalizer`, `ergebnis/json-normalizer` instead of `localheinz/json-normalizer`, and `ergebnis/json-printer` instead of `localheinz/json-printer` ([#261]), by [@localheinz]
* Removed default values for parameters `$formatter` and `$differ` of constructor of `Ergebnis\Composer\Normalize\Command\NormalizeCommand`  ([#262]), by [@localheinz]
* Renamed vendor namespace `Localheinz` to `Ergebnis` after move to [@ergebnis] ([#267]), by [@localheinz]

  Run

  ```
  $ composer remove localheinz/composer-normalize
  ```

  and

  ```
  $ composer require ergebnis/composer-normalize
  ```

  to update.

  Run

  ```
  $ find . -type f -exec sed -i '.bak' 's/Localheinz\\Composer\\Normalizer/Ergebnis\\Composer\\Normalize/g' {} \;
  ```

  to replace occurrences of `Localheinz\Composer\Normalize` with `Ergebnis\Composer\Normalize`.

  Run

  ```
  $ find -type f -name '*.bak' -delete
  ```

  to delete backup files created in the previous step.
* Marked `Ergebnis\Composer\Normalize\Command\NormalizeCommand` and `Ergebnis\Composer\Normalize\Command\SchemaUriResolver` as internal to allow modifications without the need for major releases ([#270]), by [@localheinz]

### Fixed

* Dropped support for PHP 7.1 ([#235]), by [@localheinz]

## [`1.3.1`][1.3.1]

For a full diff see [`1.3.0...1.3.1`][1.3.0...1.3.1]

### Fixed

* Started using `localheinz/diff` to avoid issues using `sebastian/diff` ([#207]), by [@localheinz]

## [`1.3.0`][1.3.0]

For a full diff see [`1.2.0...1.3.0`][1.2.0...1.3.0]

### Changed

* Resolve local and fall back to remote schema so that command works offline and behind proxies ([#190]), by [@localheinz]

## [`1.2.0`][1.2.0]

For a full diff see [`1.1.4...1.2.0`][1.1.4...1.2.0]

### Changed

* Started using the `StrictUnifiedDiffOutputBuilder` when available to create more condensed diffs when using the `--dry-run` option ([#80]), by [@localheinz]

## [`1.1.4`][1.1.4]

For a full diff see [`1.1.3...1.1.4`][1.1.3...1.1.4]

### Fixed

* Removed requirement for `composer.json` to be writable when using the `--dry-run` option ([#177]), by [@localheinz]

## [`1.1.3`][1.1.3]

For a full diff see [`1.1.2...1.1.3`][1.1.2...1.1.3]

### Fixed

* Reversed use of red and green for rendering diff when using the `--dry-run` option ([#173]), by [@TravisCarden]

## [`1.1.2`][1.1.2]

For a full diff see [`1.1.1...1.1.2`][1.1.1...1.1.2]

### Fixed

* Reverted deprecation of the `file` argument of the `NormalizeCommand` as it turns out that the same functionality can _not_ be achieved using the `--working-dir` option ([#166]), by [@localheinz]

## [`1.1.1`][1.1.1]

For a full diff see [`1.1.0...1.1.1`][1.1.0...1.1.1]

### Removed

* Updated [`localheinz/composer-json-normalizer`](http://github.com/localheinz/composer-json-normalizer), which effectively removed a dependency on [`composer/composer`](https://github.com/composer/composer) ([#157]), by [@localheinz]

## [`1.1.0`][1.1.0]

For a full diff see [`1.0.0...1.1.0`][1.0.0...1.1.0]

### Deprecated

* Deprecated the `file` argument of the `NormalizeCommand` as the same functionality can be achieved using the `--working-dir` option ([#145]), by [@localheinz]

### Fixed

* Force reading `composer.json` and `composer.lock` after normalization to ensure `composer.lock` is updated when not fresh after normalization ([#139]), by [@localheinz]

## [`1.0.0`][1.0.0]

For a full diff see [`0.9.0...1.0.0`][0.9.0...1.0.0]

### Added

* Added this changelog ([#94]), by [@localheinz]

### Removed

* Removed normalizers after extracting package [`localheinz/composer-json-normalizer`](https://github.com/localheinz/composer-json-normalizer) ([#106]), by [@localheinz]

## [`0.9.0`][0.9.0]

For a full diff see [`0.8.0...0.9.0`][0.8.0...0.9.0]

### Changed

* The `ConfigHashNormalizer` now also sorts the `scripts-descriptions` section ([#89]), by [@localheinz]

### Fixed

* When validation of `composer.lock` fails prior to normalization, it is now recommended to update the lock file only ([#86]), by [@svenluijten]

## [`0.8.0`][0.8.0]

For a full diff see [`0.7.0...0.8.0`][0.7.0...0.8.0]

### Changed

* The `ConfigHashNormalizer` now also sorts the `extra` section ([#60]), by [@localheinz]

## [`0.7.0`][0.7.0]

For a full diff see [`0.6.0...0.7.0`][0.6.0...0.7.0]

### Changed

* Updated `localheinz/json-normalizer`, which now sniffs the new-line character and uses it for printing instead of using `PHP_EOL` ([#62]), by [@localheinz]

## [`0.6.0`][0.6.0]

For a full diff see [`0.5.0...0.6.0`][0.5.0...0.6.0]

### Added

* Added a `file` argument to the `NormalizeCommand`, so the path to `composer.json` can be specified now, ([#51]), by [@localheinz]

## [`0.5.0`][0.5.0]

For a full diff see [`0.4.0...0.5.0`][0.4.0...0.5.0]

### Changed

* Updated `localheinz/json-normalizer`, which significantly improves the `SchemaNormalizer` employed to do the major normalization of `composer.json` ([#42]), by [@localheinz]

## [`0.4.0`][0.4.0]

For a full diff see [`0.3.0...0.4.0`][0.3.0...0.4.0]

### Added

* Added `--dry-run` option, which allows usage in Continuous Integration systems, as it renders a diff and exits with a non-zero exit code ([#38]), by [@localheinz]

## [`0.3.0`][0.3.0]

For a full diff see [`0.2.0...0.3.0`][0.2.0...0.3.0]

### Fixed

* Dropped support for PHP 7.0, which allows proper handling of empty PSR-4 namespace prefixes ([#30]), by [@localheinz]

## [`0.2.0`][0.2.0]

For a full diff see [`0.1.0...0.2.0`][0.1.0...0.2.0]

### Added

* Added `--no-update-lock` option, which allows skipping the update of `composer.lock` after normalization ([#28]), by [@localheinz]
* Added the `VersionConstraintNormalizer`, which normalizes version constraints ([#18]), by [@localheinz]

### Fixed

* Using the `--no-scripts` option when invoking the `UpdateCommand` to update `composer.lock` ([#19]), by [@localheinz]

## [`0.1.0`][0.1.0]

For a full diff see [`81bc3a8...0.1.0`][81bc3a8...0.1.0].

### Added

* Added `NormalizeCommand` ([#1]), by [@localheinz]
* Added `ConfigHashNormalizer`, which sorts entries in the `config` section by key ([#2]), by [@localheinz]
* Added the `NormalizePlugin`, which provides the `NormalizeCommand` ([#3]), by [@localheinz]
* Added the `PackageHashNormalizer` which sorts packages in the `conflict`, `provide`, `replaces`, `require`, `require-dev`, and `suggest` sections using the same algorithm that is used by the `sort-packages` option of composer itself ([#6]), by [@localheinz]
* Added the `BinNormalizer`, which sorts entries in the `bin` section by
* Added the `ComposerJsonNormalizer`, which composes all of the above normalizers along with the `SchemaNormalizer`, to normalize `composer.json` according to its underlying JSON schema ([#8] and [#10]), by [@localheinz]

[0.1.0]: https://github.com/ergebnis/composer-normalize/releases/tag/0.1.0
[0.2.0]: https://github.com/ergebnis/composer-normalize/releases/tag/0.2.0
[0.3.0]: https://github.com/ergebnis/composer-normalize/releases/tag/0.3.0
[0.4.0]: https://github.com/ergebnis/composer-normalize/releases/tag/0.4.0
[0.5.0]: https://github.com/ergebnis/composer-normalize/releases/tag/0.5.0
[0.6.0]: https://github.com/ergebnis/composer-normalize/releases/tag/0.6.0
[0.7.0]: https://github.com/ergebnis/composer-normalize/releases/tag/0.7.0
[0.8.0]: https://github.com/ergebnis/composer-normalize/releases/tag/0.8.0
[0.9.0]: https://github.com/ergebnis/composer-normalize/releases/tag/0.9.0
[1.0.0]: https://github.com/ergebnis/composer-normalize/releases/tag/1.0.0
[1.1.0]: https://github.com/ergebnis/composer-normalize/releases/tag/1.1.0
[1.1.1]: https://github.com/ergebnis/composer-normalize/releases/tag/1.1.1
[1.1.2]: https://github.com/ergebnis/composer-normalize/releases/tag/1.1.2
[1.1.3]: https://github.com/ergebnis/composer-normalize/releases/tag/1.1.3
[1.1.4]: https://github.com/ergebnis/composer-normalize/releases/tag/1.1.4
[1.2.0]: https://github.com/ergebnis/composer-normalize/releases/tag/1.2.0
[1.3.0]: https://github.com/ergebnis/composer-normalize/releases/tag/1.3.0
[1.3.1]: https://github.com/ergebnis/composer-normalize/releases/tag/1.3.1
[2.0.0]: https://github.com/ergebnis/composer-normalize/releases/tag/2.0.0
[2.0.1]: https://github.com/ergebnis/composer-normalize/releases/tag/2.0.1
[2.0.2]: https://github.com/ergebnis/composer-normalize/releases/tag/2.0.2

[81bc3a8...0.1.0]: https://github.com/ergebnis/composer-normalize/compare/81bc3a8...0.1.0
[0.1.0...0.2.0]: https://github.com/ergebnis/composer-normalize/compare/0.1.0...0.2.0
[0.2.0...0.3.0]: https://github.com/ergebnis/composer-normalize/compare/0.2.0...0.3.0
[0.3.0...0.4.0]: https://github.com/ergebnis/composer-normalize/compare/0.3.0...0.4.0
[0.4.0...0.5.0]: https://github.com/ergebnis/composer-normalize/compare/0.4.0...0.5.0
[0.5.0...0.6.0]: https://github.com/ergebnis/composer-normalize/compare/0.5.0...0.6.0
[0.6.0...0.7.0]: https://github.com/ergebnis/composer-normalize/compare/0.6.0...0.7.0
[0.7.0...0.8.0]: https://github.com/ergebnis/composer-normalize/compare/0.7.0...0.8.0
[0.8.0...0.9.0]: https://github.com/ergebnis/composer-normalize/compare/0.8.0...0.9.0
[0.9.0...1.0.0]: https://github.com/ergebnis/composer-normalize/compare/0.9.0...1.0.0
[1.0.0...1.1.0]: https://github.com/ergebnis/composer-normalize/compare/1.0.0...1.1.0
[1.1.0...1.1.1]: https://github.com/ergebnis/composer-normalize/compare/1.1.0...1.1.1
[1.1.1...1.1.2]: https://github.com/ergebnis/composer-normalize/compare/1.1.1...1.1.2
[1.1.2...1.1.3]: https://github.com/ergebnis/composer-normalize/compare/1.1.2...1.1.3
[1.1.3...1.1.4]: https://github.com/ergebnis/composer-normalize/compare/1.1.3...1.1.4
[1.1.4...1.2.0]: https://github.com/ergebnis/composer-normalize/compare/1.1.4...1.2.0
[1.2.0...1.3.0]: https://github.com/ergebnis/composer-normalize/compare/1.2.0...1.3.0
[1.3.0...1.3.1]: https://github.com/ergebnis/composer-normalize/compare/1.3.0...1.3.1
[1.3.1...2.0.0]: https://github.com/ergebnis/composer-normalize/compare/1.3.1...2.0.0
[2.0.0...2.0.1]: https://github.com/ergebnis/composer-normalize/compare/2.0.0...2.0.1
[2.0.1...2.0.2]: https://github.com/ergebnis/composer-normalize/compare/2.0.1...2.0.2
[2.0.2...master]: https://github.com/ergebnis/composer-normalize/compare/2.0.2...master

[#1]: https://github.com/ergebnis/composer-normalize/pull/1
[#2]: https://github.com/ergebnis/composer-normalize/pull/2
[#3]: https://github.com/ergebnis/composer-normalize/pull/3
[#6]: https://github.com/ergebnis/composer-normalize/pull/6
[#8]: https://github.com/ergebnis/composer-normalize/pull/8
[#10]: https://github.com/ergebnis/composer-normalize/pull/10
[#18]: https://github.com/ergebnis/composer-normalize/pull/18
[#19]: https://github.com/ergebnis/composer-normalize/pull/19
[#28]: https://github.com/ergebnis/composer-normalize/pull/28
[#30]: https://github.com/ergebnis/composer-normalize/pull/30
[#38]: https://github.com/ergebnis/composer-normalize/pull/38
[#42]: https://github.com/ergebnis/composer-normalize/pull/42
[#51]: https://github.com/ergebnis/composer-normalize/pull/51
[#60]: https://github.com/ergebnis/composer-normalize/pull/60
[#62]: https://github.com/ergebnis/composer-normalize/pull/62
[#80]: https://github.com/ergebnis/composer-normalize/pull/80
[#86]: https://github.com/ergebnis/composer-normalize/pull/86
[#89]: https://github.com/ergebnis/composer-normalize/pull/89
[#94]: https://github.com/ergebnis/composer-normalize/pull/94
[#106]: https://github.com/ergebnis/composer-normalize/pull/106
[#139]: https://github.com/ergebnis/composer-normalize/pull/139
[#145]: https://github.com/ergebnis/composer-normalize/pull/145
[#157]: https://github.com/ergebnis/composer-normalize/pull/157
[#166]: https://github.com/ergebnis/composer-normalize/pull/166
[#173]: https://github.com/ergebnis/composer-normalize/pull/173
[#177]: https://github.com/ergebnis/composer-normalize/pull/177
[#190]: https://github.com/ergebnis/composer-normalize/pull/190
[#207]: https://github.com/ergebnis/composer-normalize/pull/207
[#235]: https://github.com/ergebnis/composer-normalize/pull/235
[#261]: https://github.com/ergebnis/composer-normalize/pull/261
[#262]: https://github.com/ergebnis/composer-normalize/pull/262
[#267]: https://github.com/ergebnis/composer-normalize/pull/267
[#270]: https://github.com/ergebnis/composer-normalize/pull/270
[#273]: https://github.com/ergebnis/composer-normalize/pull/273
[#280]: https://github.com/ergebnis/composer-normalize/pull/280

[@ergebnis]: https://github.com/ergebnis
[@localheinz]: https://github.com/localheinz
[@svenluijten]: https://github.com/svenluijten
[@TravisCarden]: https://github.com/TravisCarden
