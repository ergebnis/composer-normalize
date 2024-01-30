# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/), and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## Unreleased

For a full diff see [`2.42.0...main`][2.42.0...main].

## [`2.42.0`][2.42.0]

For a full diff see [`2.41.1...2.42.0`][2.40.0...2.42.0].

### Changed

- Required `ergebnis/json:^1.2.0` ([#1273]), by [@dependabot]
- Required `ergebnis/json-printer:^3.5.0` ([#1275]), by [@dependabot]
- Required `ergebnis/json-normalizer:^4.5.0` ([#1277]), by [@localheinz]
- Added support for PHP 8.0 ([#1278]), by [@localheinz]
- Added support for PHP 7.4 ([#1279]), by [@localheinz]

## [`2.41.1`][2.41.1]

For a full diff see [`2.41.0...2.41.1`][2.41.0...2.41.1].

### Fixed

- Required `ergebnis/json-normalizer:^4.4.1` ([#1243]), by [@dependabot]

## [`2.41.0`][2.41.0]

For a full diff see [`2.40.0...2.41.0`][2.40.0...2.41.0].

### Changed

- Required `ergebnis/json-normalizer:^4.4.0` ([#1241]), by [@dependabot]

### Fixed

- Updated `composer/composer` ([#1237]), by [@localheinz]

## [`2.40.0`][2.40.0]

For a full diff see [`2.39.0...2.40.0`][2.39.0...2.40.0].

### Changed

- Updated `schema.json` ([#1204]), by [@ergebnis-bot]

### Fixed

- Prevented installation of `symfony/console:^7.0.0` ([#1234]), by [@localheinz]

## [`2.39.0`][2.39.0]

For a full diff see [`2.38.0...2.39.0`][2.38.0...2.39.0].

### Changed

- Required `ergebnis/json:^1.1.0` ([#1191]), by [@localheinz]
- Required `ergebnis/json-normalizer:^4.3.0` ([#1192]), by [@localheinz]
- Required `ergebnis/json-printer:^3.4.0` ([#1195]), by [@localheinz]

## [`2.38.0`][2.38.0]

For a full diff see [`2.37.0...2.38.0`][2.37.0...2.38.0].

### Changed

- Added support for PHP 8.3  ([#1189]), by [@localheinz]

### Fixed

- Adjusted `Command\NormalizeCommand` to respect `--no-ansi`, `--no-plugins`, `--no-scripts` options ([#1184]), by [@mxr576]
- Updated `composer/composer` ([#1188]), by [@localheinz]

## [`2.37.0`][2.37.0]

For a full diff see [`2.36.0...2.37.0`][2.36.0...2.37.0].

### Changed

- Updated `schema.json` ([#1170]), by [@ergebnis-bot]

### Fixed

- Updated `composer/composer` ([#1171]), by [@localheinz]

## [`2.36.0`][2.36.0]

For a full diff see [`2.35.0...2.36.0`][2.35.0...2.36.0].

### Changed

- Updated `schema.json` ([#1155]), by [@ergebnis-bot]
- Required `composer/composer:2.6.1` for compiling `composer-normalize.phar` ([#1158]), by [@localheinz]

## [`2.35.0`][2.35.0]

For a full diff see [`2.34.0...2.35.0`][2.34.0...2.35.0].

### Changed

- Started passing through `--no-plugins` and `--no-scripts` options ([#1141]), by [@mxr576]

## [`2.34.0`][2.34.0]

For a full diff see [`2.33.0...2.34.0`][2.33.0...2.34.0].

### Changed

- Updated `schema.json` ([#1136]), by [@ergebnis-bot]

## [`2.33.0`][2.33.0]

For a full diff see [`2.32.0...2.33.0`][2.32.0...2.33.0].

### Changed

- Required `ergebnis/json-normalizer:^4.2.0` ([#1127]), by [@dependabot]

## [`2.32.0`][2.32.0]

For a full diff see [`2.31.0...2.32.0`][2.31.0...2.32.0].

### Changed

- Dropped support for PHP 8.0 ([#1118]), by [@localheinz]

### Fixed

- Required `composer/composer:2.5.8` for compiling `composer-normalize.phar` ([#1125]), by [@localheinz]

## [`2.31.0`][2.31.0]

For a full diff see [`2.30.2...2.31.0`][2.30.2...2.31.0].

### Changed

- Updated `schema.json` ([#1070]), by [@ergebnis-bot]
- Required `ergebnis/json-normalizer:^4.1.0` ([#1095]), by [@dependabot]
- Started passing through `--no-ansi` option to `UpdateCommand` ([#827]), by [@localheinz]

### Fixed

- Required `composer/composer:2.5.5` for compiling `composer-normalize.phar` ([#1094]), by [@localheinz]

## [`2.30.2`][2.30.2]

For a full diff see [`2.30.1...2.30.2`][2.30.1...2.30.2].

### Fixed

- Required `ergebnis/json-normalizer:^4.0.2` ([#1062]), by [@localheinz]

## [`2.30.1`][2.30.1]

For a full diff see [`2.30.0...2.30.1`][2.30.0...2.30.1].

### Fixed

- Required `ergebnis/json-normalizer:^4.0.1` ([#1060]), by [@localheinz]

## [`2.30.0`][2.30.0]

For a full diff see [`2.29.0...2.30.0`][2.29.0...2.30.0].

### Changed

- Started injecting `Printer\Printer` instead of `Formatter\Formatter` into `NormalizeCommand` ([#1008]), by [@ergebnis-bot]
- Required `composer/composer:2.5.1` for compiling `composer-normalize.phar` ([#1020]), by [@localheinz]
- Required `ergebnis/json-normalizer:^4.0.0` ([#1056]), by [@dependabot]

## [`2.29.0`][2.29.0]

For a full diff see [`2.28.3...2.29.0`][2.28.3...2.29.0].

### Changed

- Updated `schema.json` ([#959]), by [@ergebnis-bot]
- Dropped support for PHP 7.4 ([#998]), by [@localheinz]
- Required `composer/composer:2.4.4` for compiling `composer-normalize.phar` ([#1004]), by [@localheinz]

## [`2.28.3`][2.28.3]

For a full diff see [`2.28.2...2.28.3`][2.28.2...2.28.3].

### Fixed

- Required `composer/composer:2.3.9` for compiling `composer-normalize.phar` ([#942]), by [@localheinz]

## [`2.28.2`][2.28.2]

For a full diff see [`2.28.1...2.28.2`][2.28.1...2.28.2].

### Fixed

- Required `composer/composer:2.3.8` for compiling `composer-normalize.phar` ([#941]), by [@localheinz]

## [`2.28.1`][2.28.1]

For a full diff see [`2.28.0...2.28.1`][2.28.0...2.28.1].

### Removed

- Removed banner ([#938]), by [@localheinz]

## [`2.28.0`][2.28.0]

For a full diff see [`2.27.0...2.28.0`][2.27.0...2.28.0].

### Changed

- Updated `schema.json` ([#933]), by [@ergebnis-bot]

## [`2.27.0`][2.27.0]

For a full diff see [`2.26.0...2.27.0`][2.26.0...2.27.0].

### Changed

- Updated `schema.json` ([#930]), by [@ergebnis-bot]

## [`2.26.0`][2.26.0]

For a full diff see [`2.25.2...2.26.0`][2.25.2...2.26.0].

### Changed

- Updated `schema.json` ([#923]), by [@ergebnis-bot]

## [`2.25.2`][2.25.2]

For a full diff see [`2.25.1...2.25.2`][2.25.1...2.25.2].

### Changed

- Updated `schema.json` ([#916]), by [@ergebnis-bot]
- Required `composer/composer:2.3.5` for compiling `composer-normalize.phar` ([#922]), by [@localheinz]

### Fixed

- Updated `justinrainbow/json-schema` ([#920]), by [@dependabot]

## [`2.25.1`][2.25.1]

For a full diff see [`2.25.0...2.25.1`][2.25.0...2.25.1].

### Changed

- Required `composer/composer:2.3.1` for compiling `composer-normalize.phar` ([#915]), by [@localheinz]

## [`2.25.0`][2.25.0]

For a full diff see [`2.24.1...2.25.0`][2.24.1...2.25.0].

### Changed

- Updated `schema.json` ([#912]), by [@ergebnis-bot]
- Required `composer/composer:2.3.0` for compiling `composer-normalize.phar` ([#913]), by [@localheinz]

## [`2.24.1`][2.24.1]

For a full diff see [`2.24.0...2.24.1`][2.24.0...2.24.1].

### Fixed

- Required `composer/composer:2.2.9` for compiling `composer-normalize.phar` ([#904]), by [@localheinz]
- Updated `humbug/box` ([#905]), by [@localheinz]

## [`2.24.0`][2.24.0]

For a full diff see [`2.23.1...2.24.0`][2.23.1...2.24.0].

### Added

- Added Stand with Ukraine banner ([#899]), by [@localheinz]

## [`2.23.1`][2.23.1]

For a full diff see [`2.23.0...2.23.1`][2.23.0...2.23.1].

### Fixed

- Required `composer/composer:2.2.5` for compiling `composer-normalize.phar` ([#871]), by [@localheinz]
- Prevented updates of `ergebnis/json-normalizer` beyond `2.1.0` for now ([#877]), by [@localheinz]

## [`2.23.0`][2.23.0]

For a full diff see [`2.22.0...2.23.0`][2.22.0...2.23.0].

### Changed

- Updated `ergebnis/json-normalizer` ([#864]), by [@localheinz]

### Fixed

- Required `composer/composer:2.2.3` for compiling `composer-normalize.phar` ([#863]), by [@localheinz]

## [`2.22.0`][2.22.0]

For a full diff see [`2.21.0...2.22.0`][2.21.0...2.22.0].

### Changed

- Required `ergebnis/json-normalizer:^2.0.0` ([#858]), by [@dependabot]

## [`2.21.0`][2.21.0]

For a full diff see [`2.20.0...2.21.0`][2.20.0...2.21.0].

### Changed

- Dropped support for PHP 7.3 ([#852]), by [@localheinz]

## [`2.20.0`][2.20.0]

For a full diff see [`2.19.0...2.20.0`][2.19.0...2.20.0].

### Changed

- Dropped support for PHP 7.2 ([#845]), by [@localheinz]

## [`2.19.0`][2.19.0]

For a full diff see [`2.18.0...2.19.0`][2.18.0...2.19.0].

### Changed

- Required `composer/composer:2.2.1` for compiling `composer-normalize.phar` ([#842]), by [@localheinz]

## [`2.18.0`][2.18.0]

For a full diff see [`2.17.0...2.18.0`][2.17.0...2.18.0].

### Changed

- Updated `schema.json` ([#829]), by [@ergebnis-bot]

## [`2.17.0`][2.17.0]

For a full diff see [`2.16.0...2.17.0`][2.16.0...2.17.0].

### Changed

- Updated `schema.json` ([#816]), by [@ergebnis-bot]

### Fixed

- Required `composer/composer:2.1.14` for compiling `composer-normalize.phar` ([#825]), by [@localheinz]

## [`2.16.0`][2.16.0]

For a full diff see [`2.15.0...2.16.0`][2.15.0...2.16.0].

### Changed

- Required `composer/composer:2.1.12` for compiling `composer-normalize.phar` ([#804]), by [@localheinz]
- Dropped support for `composer/composer:^1.0.0` ([#807]), by [@localheinz]

## [`2.15.0`][2.15.0]

For a full diff see [`2.14.0...2.15.0`][2.14.0...2.15.0].

### Changed

- Updated `schema.json` ([#754]), by [@ergebnis-bot]

## [`2.14.0`][2.14.0]

For a full diff see [`2.13.4...2.14.0`][2.13.4...2.14.0].

### Changed

- Updated `schema.json` ([#744]), by [@ergebnis-bot]

### Fixed

- Updated `composer/composer` ([#750]), by [@localheinz]

## [`2.13.4`][2.13.4]

For a full diff see [`2.13.3...2.13.4`][2.13.3...2.13.4].

### Fixed

- Required `composer/composer:2.0.13` for compiling `composer-normalize.phar` ([#743]), by [@localheinz]

## [`2.13.3`][2.13.3]

For a full diff see [`2.13.2...2.13.3`][2.13.2...2.13.3].

### Fixed

- Required `ergebnis/json-normalizer:^1.0.3` which correctly sorts `composer-plugin-api` ([#707]), by [@dependabot]

## [`2.13.2`][2.13.2]

For a full diff see [`2.13.1...2.13.2`][2.13.1...2.13.2].

### Fixed

- Required `ergebnis/json-normalizer:^1.0.2` which ignores the `config.preferred-install` hash only instead of all properties with the name `preferred-install` ([#647]), by [@localheinz]

## [`2.13.1`][2.13.1]

For a full diff see [`2.13.0...2.13.1`][2.13.0...2.13.1].

:clown_face: Made a mistake tagging this release *before- pulling changes merged into `main`.

## [`2.13.0`][2.13.0]

For a full diff see [`2.12.2...2.13.0`][2.12.2...2.13.0].

### Changed

- Brought back support for `composer/composer:^1.0.0` ([#644]), by [@localheinz]

## [`2.12.2`][2.12.2]

For a full diff see [`2.12.1...2.12.2`][2.12.1...2.12.2].

### Fixed

- Required `ergebnis/json-normalizer:^1.0.1` which ignores the `preferred-install` hash when sorting configuration hashes by key ([#646]), by [@dependabot]

## [`2.12.1`][2.12.1]

For a full diff see [`2.12.0...2.12.1`][2.12.0...2.12.1].

### Fixed

- Show version of plugin instead of version of `Composer\Console\Application` when running as development dependency ([#643]), by [@localheinz]

## [`2.12.0`][2.12.0]

For a full diff see [`2.11.0...2.12.0`][2.11.0...2.12.0].

### Added

- Started showing plugin and author name when running `composer normalize` ([#641]), by [@localheinz]

### Changed

- Required `ergebnis/json-normalizer:^1.0.0` which allows recursively sorting config hashes ([#634]), by [@dependabot]

### Fixed

- Required `composer/composer:2.0.8` for `composer-normalize.phar` ([#640]), by [@localheinz]

## [`2.11.0`][2.11.0]

For a full diff see [`2.10.0...2.11.0`][2.10.0...2.11.0].

### Changed

- Updated `schema.json` ([#615]), by [@ergebnis-bot]

## [`2.10.0`][2.10.0]

For a full diff see [`2.9.1...2.10.0`][2.9.1...2.10.0].

### Added

- Allowed configuration via composer extra ([#608]), by [@localheinz]

## [`2.9.1`][2.9.1]

For a full diff see [`2.9.0...2.9.1`][2.9.0...2.9.1].

### Fixed

- Required at least `composer/composer:^1.10.17` and used `composer/composer:1.10.17` for `composer-normalize.phar` ([#596]), by [@localheinz]
- Dropped support for `composer/composer:^1.0.0` ([#597]), by [@localheinz]

## [`2.9.0`][2.9.0]

For a full diff see [`2.8.2...2.9.0`][2.8.2...2.9.0].

### Changed

- Updated `schema.json` ([#572]), by [@ergebnis-bot]

### Fixed

- Required at least `composer/composer:^1.10.15` and used `composer/composer:1.10.15` for `composer-normalize.phar` ([#582]), by [@localheinz]

## [`2.8.2`][2.8.2]

For a full diff see [`2.8.1...2.8.2`][2.8.1...2.8.2].

### Changed

- Require at least `composer/composer:^1.10.13` ([#554]), by [@localheinz]

## [`2.8.1`][2.8.1]

For a full diff see [`2.8.0...2.8.1`][2.8.0...2.8.1].

### Changed

- Dropped support for PHP 7.1 ([#529]), by [@localheinz]

## [`2.8.0`][2.8.0]

For a full diff see [`2.7.0...2.8.0`][2.7.0...2.8.0].

### Changed

- Updated `schema.json` ([#526]), by [@ergebnis-bot]

## [`2.7.0`][2.7.0]

For a full diff see [`2.6.1...2.7.0`][2.6.1...2.7.0].

### Added

- Added `--no-check-lock` option which allows skipping validation of `composer.lock` ([#515]), by [@localheinz]

### Changed

- Updated `schema.json` ([#512]), by [@ergebnis-bot]

## [`2.6.1`][2.6.1]

For a full diff see [`2.6.0...2.6.1`][2.6.0...2.6.1].

### Fixed

- Added support for PHP 8.0, for real ([#484], [#485], [#487]), by [@dependabot]

## [`2.6.0`][2.6.0]

For a full diff see [`2.5.2...2.6.0`][2.5.2...2.6.0].

### Added

- Added support for PHP 8.0 ([#465]), by [@core23]

## [`2.5.2`][2.5.2]

For a full diff see [`2.5.1...2.5.2`][2.5.1...2.5.2].

### Fixed

- Started ignoring platform requirements when updating the lock file ([#481]), by [@localheinz]

## [`2.5.1`][2.5.1]

For a full diff see [`2.5.0...2.5.1`][2.5.0...2.5.1].

### Fixed

- Started updating lock files with a new `Composer\Console\Application` instead of reusing the current instance ([#420]), by [@localheinz]
- Stopped using the deprecated `--no-suggest` option when updating the lock file ([#422]), by [@localheinz]
- Started relaxing schema in place to avoid issues resolving references and the like on Windows ([#424]), by [@localheinz]

## [`2.5.0`][2.5.0]

For a full diff see [`2.4.0...2.5.0`][2.4.0...2.5.0].

### Changed

- Apply lax validation to `composer.json` ([#416]), by [@localheinz]

## [`2.4.0`][2.4.0]

For a full diff see [`2.3.2...2.4.0`][2.3.2...2.4.0].

### Changed

- Started showing validation error messages as obtained from validation instead of relying on on executing composer validate ([#406]), by [@localheinz]
- Made plugin compatible with `composer/composer:^2.0.0`  ([#412]), by [@localheinz]

## [`2.3.2`][2.3.2]

For a full diff see [`2.3.1...2.3.2`][2.3.1...2.3.2].

### Fixed

- Fixed a reference that prevented an upload of release assets ([#380]), by [@localheinz]

## [`2.3.1`][2.3.1]

For a full diff see [`2.3.0...2.3.1`][2.3.0...2.3.1].

### Fixed

- Updated `composer/composer` ([#379]), by [@localheinz]

## [`2.3.0`][2.3.0]

For a full diff see [`2.2.4...2.3.0`][2.2.4...2.3.0].

### Changed

- Updated `schema.json` ([#374]), by [@ergebnis-bot]

## [`2.2.4`][2.2.4]

For a full diff see [`2.2.3...2.2.4`][2.2.3...2.2.4].

### Fixed

- Use real path to `schema.json` ([#364]), by [@localheinz]

## [`2.2.3`][2.2.3]

For a full diff see [`2.2.2...2.2.3`][2.2.2...2.2.3].

### Changed

- Updated `schema.json` ([#354]), by [@ergebnis-bot]

## [`2.2.2`][2.2.2]

For a full diff see [`2.2.1...2.2.2`][2.2.1...2.2.2].

### Changed

- Updated `schema.json` ([#322]), by [@localheinz]

## [`2.2.1`][2.2.1]

For a full diff see [`2.2.0...2.2.1`][2.2.0...2.2.1].

### Changed

- Removed dependency on `ergebnis/composer-json-normalizer` ([#316]), by [@localheinz]

## [`2.2.0`][2.2.0]

For a full diff see [`2.1.2...2.2.0`][2.1.2...2.2.0].

### Added

- Added `--diff` option ([#303]), by [@localheinz]

## [`2.1.2`][2.1.2]

For a full diff see [`2.1.1...2.1.2`][2.1.1...2.1.2].

### Fixed

- Allow passing argument and options to the command ([#301]), by [@localheinz]

## [`2.1.1`][2.1.1]

For a full diff see [`2.1.0...2.1.1`][2.1.0...2.1.1].

### Fixed

- Actually run `composer validate` to show validation errors when `composer.json` is not valid according to its schema ([#297]), by [@localheinz]

## [`2.1.0`][2.1.0]

For a full diff see [`2.0.2...2.1.0`][2.0.2...2.1.0].

### Added

- Started compiling, signing, and uploading `composer-normalize.phar` and `composer-normalize.phar.asc` to release assets when a tag is pushed ([#292]), by [@localheinz]

## [`2.0.2`][2.0.2]

For a full diff see [`2.0.1...2.0.2`][2.0.1...2.0.2].

### Fixed

- Brought back support for PHP 7.1 ([#280]), by [@localheinz]

## [`2.0.1`][2.0.1]

For a full diff see [`2.0.0...2.0.1`][2.0.0...2.0.1]

## Changed

- Removed `Ergebnis\Composer\Normalize\Command\SchemaUriResolver` and checked in `schema.json` instead ([#273]), by [@localheinz]

## [`2.0.0`][2.0.0]

For a full diff see [`1.3.1...2.0.0`][1.3.1...2.0.0].

## Changed

- Started using `ergebnis/composer-json-normalizer` instead of `localheinz/composer-json-normalizer`, `ergebnis/json-normalizer` instead of `localheinz/json-normalizer`, and `ergebnis/json-printer` instead of `localheinz/json-printer` ([#261]), by [@localheinz]
- Removed default values for parameters `$formatter` and `$differ` of constructor of `Ergebnis\Composer\Normalize\Command\NormalizeCommand`  ([#262]), by [@localheinz]
- Renamed vendor namespace `Localheinz` to `Ergebnis` after move to [@ergebnis] ([#267]), by [@localheinz]

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
- Marked `Ergebnis\Composer\Normalize\Command\NormalizeCommand` and `Ergebnis\Composer\Normalize\Command\SchemaUriResolver` as internal to allow modifications without the need for major releases ([#270]), by [@localheinz]

### Fixed

- Dropped support for PHP 7.1 ([#235]), by [@localheinz]

## [`1.3.1`][1.3.1]

For a full diff see [`1.3.0...1.3.1`][1.3.0...1.3.1].

### Fixed

- Started using `localheinz/diff` to avoid issues using `sebastian/diff` ([#207]), by [@localheinz]

## [`1.3.0`][1.3.0]

For a full diff see [`1.2.0...1.3.0`][1.2.0...1.3.0].

### Changed

- Resolve local and fall back to remote schema so that command works offline and behind proxies ([#190]), by [@localheinz]

## [`1.2.0`][1.2.0]

For a full diff see [`1.1.4...1.2.0`][1.1.4...1.2.0].

### Changed

- Started using the `StrictUnifiedDiffOutputBuilder` when available to create more condensed diffs when using the `--dry-run` option ([#80]), by [@localheinz]

## [`1.1.4`][1.1.4]

For a full diff see [`1.1.3...1.1.4`][1.1.3...1.1.4].

### Fixed

- Removed requirement for `composer.json` to be writable when using the `--dry-run` option ([#177]), by [@localheinz]

## [`1.1.3`][1.1.3]

For a full diff see [`1.1.2...1.1.3`][1.1.2...1.1.3].

### Fixed

- Reversed use of red and green for rendering diff when using the `--dry-run` option ([#173]), by [@TravisCarden]

## [`1.1.2`][1.1.2]

For a full diff see [`1.1.1...1.1.2`][1.1.1...1.1.2].

### Fixed

- Reverted deprecation of the `file` argument of the `NormalizeCommand` as it turns out that the same functionality can _not_ be achieved using the `--working-dir` option ([#166]), by [@localheinz]

## [`1.1.1`][1.1.1]

For a full diff see [`1.1.0...1.1.1`][1.1.0...1.1.1].

### Removed

- Updated [`localheinz/composer-json-normalizer`](http://github.com/localheinz/composer-json-normalizer), which effectively removed a dependency on [`composer/composer`](https://github.com/composer/composer) ([#157]), by [@localheinz]

## [`1.1.0`][1.1.0]

For a full diff see [`1.0.0...1.1.0`][1.0.0...1.1.0].

### Deprecated

- Deprecated the `file` argument of the `NormalizeCommand` as the same functionality can be achieved using the `--working-dir` option ([#145]), by [@localheinz]

### Fixed

- Force reading `composer.json` and `composer.lock` after normalization to ensure `composer.lock` is updated when not fresh after normalization ([#139]), by [@localheinz]

## [`1.0.0`][1.0.0]

For a full diff see [`0.9.0...1.0.0`][0.9.0...1.0.0].

### Added

- Added this changelog ([#94]), by [@localheinz]

### Removed

- Removed normalizers after extracting package [`localheinz/composer-json-normalizer`](https://github.com/localheinz/composer-json-normalizer) ([#106]), by [@localheinz]

## [`0.9.0`][0.9.0]

For a full diff see [`0.8.0...0.9.0`][0.8.0...0.9.0].

### Changed

- The `ConfigHashNormalizer` now also sorts the `scripts-descriptions` section ([#89]), by [@localheinz]

### Fixed

- When validation of `composer.lock` fails prior to normalization, it is now recommended to update the lock file only ([#86]), by [@svenluijten]

## [`0.8.0`][0.8.0]

For a full diff see [`0.7.0...0.8.0`][0.7.0...0.8.0].

### Changed

- The `ConfigHashNormalizer` now also sorts the `extra` section ([#60]), by [@localheinz]

## [`0.7.0`][0.7.0]

For a full diff see [`0.6.0...0.7.0`][0.6.0...0.7.0].

### Changed

- Updated `localheinz/json-normalizer`, which now sniffs the new-line character and uses it for printing instead of using `PHP_EOL` ([#62]), by [@localheinz]

## [`0.6.0`][0.6.0]

For a full diff see [`0.5.0...0.6.0`][0.5.0...0.6.0].

### Added

- Added a `file` argument to the `NormalizeCommand`, so the path to `composer.json` can be specified now, ([#51]), by [@localheinz]

## [`0.5.0`][0.5.0]

For a full diff see [`0.4.0...0.5.0`][0.4.0...0.5.0].

### Changed

- Updated `localheinz/json-normalizer`, which significantly improves the `SchemaNormalizer` employed to do the major normalization of `composer.json` ([#42]), by [@localheinz]

## [`0.4.0`][0.4.0]

For a full diff see [`0.3.0...0.4.0`][0.3.0...0.4.0].

### Added

- Added `--dry-run` option, which allows usage in Continuous Integration systems, as it renders a diff and exits with a non-zero exit code ([#38]), by [@localheinz]

## [`0.3.0`][0.3.0]

For a full diff see [`0.2.0...0.3.0`][0.2.0...0.3.0].

### Fixed

- Dropped support for PHP 7.0, which allows proper handling of empty PSR-4 namespace prefixes ([#30]), by [@localheinz]

## [`0.2.0`][0.2.0]

For a full diff see [`0.1.0...0.2.0`][0.1.0...0.2.0].

### Added

- Added `--no-update-lock` option, which allows skipping the update of `composer.lock` after normalization ([#28]), by [@localheinz]
- Added the `VersionConstraintNormalizer`, which normalizes version constraints ([#18]), by [@localheinz]

### Fixed

- Using the `--no-scripts` option when invoking the `UpdateCommand` to update `composer.lock` ([#19]), by [@localheinz]

## [`0.1.0`][0.1.0]

For a full diff see [`81bc3a8...0.1.0`][81bc3a8...0.1.0].

### Added

- Added `NormalizeCommand` ([#1]), by [@localheinz]
- Added `ConfigHashNormalizer`, which sorts entries in the `config` section by key ([#2]), by [@localheinz]
- Added the `NormalizePlugin`, which provides the `NormalizeCommand` ([#3]), by [@localheinz]
- Added the `PackageHashNormalizer` which sorts packages in the `conflict`, `provide`, `replaces`, `require`, `require-dev`, and `suggest` sections using the same algorithm that is used by the `sort-packages` option of composer itself ([#6]), by [@localheinz]
- Added the `BinNormalizer`, which sorts entries in the `bin` section by
- Added the `ComposerJsonNormalizer`, which composes all of the above normalizers along with the `SchemaNormalizer`, to normalize `composer.json` according to its underlying JSON schema ([#8] and [#10]), by [@localheinz]

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
[2.1.0]: https://github.com/ergebnis/composer-normalize/releases/tag/2.1.0
[2.1.1]: https://github.com/ergebnis/composer-normalize/releases/tag/2.1.1
[2.1.2]: https://github.com/ergebnis/composer-normalize/releases/tag/2.1.2
[2.2.0]: https://github.com/ergebnis/composer-normalize/releases/tag/2.2.0
[2.2.1]: https://github.com/ergebnis/composer-normalize/releases/tag/2.2.1
[2.2.2]: https://github.com/ergebnis/composer-normalize/releases/tag/2.2.2
[2.2.3]: https://github.com/ergebnis/composer-normalize/releases/tag/2.2.3
[2.2.4]: https://github.com/ergebnis/composer-normalize/releases/tag/2.2.4
[2.3.0]: https://github.com/ergebnis/composer-normalize/releases/tag/2.3.0
[2.3.1]: https://github.com/ergebnis/composer-normalize/releases/tag/2.3.1
[2.3.2]: https://github.com/ergebnis/composer-normalize/releases/tag/2.3.2
[2.4.0]: https://github.com/ergebnis/composer-normalize/releases/tag/2.4.0
[2.5.0]: https://github.com/ergebnis/composer-normalize/releases/tag/2.5.0
[2.5.1]: https://github.com/ergebnis/composer-normalize/releases/tag/2.5.1
[2.5.2]: https://github.com/ergebnis/composer-normalize/releases/tag/2.5.2
[2.6.0]: https://github.com/ergebnis/composer-normalize/releases/tag/2.6.0
[2.6.1]: https://github.com/ergebnis/composer-normalize/releases/tag/2.6.1
[2.7.0]: https://github.com/ergebnis/composer-normalize/releases/tag/2.7.0
[2.8.0]: https://github.com/ergebnis/composer-normalize/releases/tag/2.8.0
[2.8.1]: https://github.com/ergebnis/composer-normalize/releases/tag/2.8.1
[2.8.2]: https://github.com/ergebnis/composer-normalize/releases/tag/2.8.2
[2.9.0]: https://github.com/ergebnis/composer-normalize/releases/tag/2.9.0
[2.9.1]: https://github.com/ergebnis/composer-normalize/releases/tag/2.9.1
[2.10.0]: https://github.com/ergebnis/composer-normalize/releases/tag/2.10.0
[2.11.0]: https://github.com/ergebnis/composer-normalize/releases/tag/2.11.0
[2.12.0]: https://github.com/ergebnis/composer-normalize/releases/tag/2.12.0
[2.12.1]: https://github.com/ergebnis/composer-normalize/releases/tag/2.12.1
[2.12.2]: https://github.com/ergebnis/composer-normalize/releases/tag/2.12.2
[2.13.0]: https://github.com/ergebnis/composer-normalize/releases/tag/2.13.0
[2.13.1]: https://github.com/ergebnis/composer-normalize/releases/tag/2.13.1
[2.13.2]: https://github.com/ergebnis/composer-normalize/releases/tag/2.13.2
[2.13.3]: https://github.com/ergebnis/composer-normalize/releases/tag/2.13.3
[2.13.4]: https://github.com/ergebnis/composer-normalize/releases/tag/2.13.4
[2.14.0]: https://github.com/ergebnis/composer-normalize/releases/tag/2.14.0
[2.15.0]: https://github.com/ergebnis/composer-normalize/releases/tag/2.15.0
[2.16.0]: https://github.com/ergebnis/composer-normalize/releases/tag/2.16.0
[2.17.0]: https://github.com/ergebnis/composer-normalize/releases/tag/2.17.0
[2.18.0]: https://github.com/ergebnis/composer-normalize/releases/tag/2.18.0
[2.19.0]: https://github.com/ergebnis/composer-normalize/releases/tag/2.19.0
[2.20.0]: https://github.com/ergebnis/composer-normalize/releases/tag/2.20.0
[2.21.0]: https://github.com/ergebnis/composer-normalize/releases/tag/2.21.0
[2.22.0]: https://github.com/ergebnis/composer-normalize/releases/tag/2.22.0
[2.23.0]: https://github.com/ergebnis/composer-normalize/releases/tag/2.23.0
[2.23.1]: https://github.com/ergebnis/composer-normalize/releases/tag/2.23.1
[2.24.0]: https://github.com/ergebnis/composer-normalize/releases/tag/2.24.0
[2.24.1]: https://github.com/ergebnis/composer-normalize/releases/tag/2.24.1
[2.25.0]: https://github.com/ergebnis/composer-normalize/releases/tag/2.25.0
[2.25.1]: https://github.com/ergebnis/composer-normalize/releases/tag/2.25.1
[2.25.2]: https://github.com/ergebnis/composer-normalize/releases/tag/2.25.2
[2.26.0]: https://github.com/ergebnis/composer-normalize/releases/tag/2.26.0
[2.27.0]: https://github.com/ergebnis/composer-normalize/releases/tag/2.27.0
[2.28.0]: https://github.com/ergebnis/composer-normalize/releases/tag/2.28.0
[2.28.1]: https://github.com/ergebnis/composer-normalize/releases/tag/2.28.1
[2.28.2]: https://github.com/ergebnis/composer-normalize/releases/tag/2.28.2
[2.28.3]: https://github.com/ergebnis/composer-normalize/releases/tag/2.28.3
[2.29.0]: https://github.com/ergebnis/composer-normalize/releases/tag/2.29.0
[2.30.0]: https://github.com/ergebnis/composer-normalize/releases/tag/2.30.0
[2.30.1]: https://github.com/ergebnis/composer-normalize/releases/tag/2.30.1
[2.30.2]: https://github.com/ergebnis/composer-normalize/releases/tag/2.30.2
[2.31.0]: https://github.com/ergebnis/composer-normalize/releases/tag/2.31.0
[2.32.0]: https://github.com/ergebnis/composer-normalize/releases/tag/2.32.0
[2.33.0]: https://github.com/ergebnis/composer-normalize/releases/tag/2.33.0
[2.34.0]: https://github.com/ergebnis/composer-normalize/releases/tag/2.34.0
[2.35.0]: https://github.com/ergebnis/composer-normalize/releases/tag/2.35.0
[2.36.0]: https://github.com/ergebnis/composer-normalize/releases/tag/2.36.0
[2.37.0]: https://github.com/ergebnis/composer-normalize/releases/tag/2.37.0
[2.38.0]: https://github.com/ergebnis/composer-normalize/releases/tag/2.38.0
[2.39.0]: https://github.com/ergebnis/composer-normalize/releases/tag/2.39.0
[2.40.0]: https://github.com/ergebnis/composer-normalize/releases/tag/2.40.0
[2.41.0]: https://github.com/ergebnis/composer-normalize/releases/tag/2.41.0
[2.41.1]: https://github.com/ergebnis/composer-normalize/releases/tag/2.41.1
[2.42.0]: https://github.com/ergebnis/composer-normalize/releases/tag/2.42.0

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
[2.0.2...2.1.0]: https://github.com/ergebnis/composer-normalize/compare/2.0.2...2.1.0
[2.1.0...2.1.1]: https://github.com/ergebnis/composer-normalize/compare/2.1.0...2.1.1
[2.1.1...2.1.2]: https://github.com/ergebnis/composer-normalize/compare/2.1.1...2.1.2
[2.1.2...2.2.0]: https://github.com/ergebnis/composer-normalize/compare/2.1.2...2.2.0
[2.2.0...2.2.1]: https://github.com/ergebnis/composer-normalize/compare/2.2.0...2.2.1
[2.2.1...2.2.2]: https://github.com/ergebnis/composer-normalize/compare/2.2.1...2.2.2
[2.2.2...2.2.3]: https://github.com/ergebnis/composer-normalize/compare/2.2.2...2.2.3
[2.2.3...2.2.4]: https://github.com/ergebnis/composer-normalize/compare/2.2.3...2.2.4
[2.2.4...2.3.0]: https://github.com/ergebnis/composer-normalize/compare/2.2.4...2.3.0
[2.3.0...2.3.1]: https://github.com/ergebnis/composer-normalize/compare/2.3.0...2.3.1
[2.3.1...2.3.2]: https://github.com/ergebnis/composer-normalize/compare/2.3.1...2.3.2
[2.3.2...2.4.0]: https://github.com/ergebnis/composer-normalize/compare/2.4.0...main
[2.4.0...2.5.0]: https://github.com/ergebnis/composer-normalize/compare/2.4.0...2.5.0
[2.5.0...2.5.1]: https://github.com/ergebnis/composer-normalize/compare/2.5.0...2.5.1
[2.5.1...2.5.2]: https://github.com/ergebnis/composer-normalize/compare/2.5.1...2.5.2
[2.5.2...2.6.0]: https://github.com/ergebnis/composer-normalize/compare/2.5.2...2.6.0
[2.6.0...2.6.1]: https://github.com/ergebnis/composer-normalize/compare/2.6.0...2.6.1
[2.6.1...2.7.0]: https://github.com/ergebnis/composer-normalize/compare/2.6.1...2.7.0
[2.7.0...2.8.0]: https://github.com/ergebnis/composer-normalize/compare/2.7.0...2.8.0
[2.8.0...2.8.1]: https://github.com/ergebnis/composer-normalize/compare/2.8.0...2.8.1
[2.8.1...2.8.2]: https://github.com/ergebnis/composer-normalize/compare/2.8.1...2.8.2
[2.8.2...2.9.0]: https://github.com/ergebnis/composer-normalize/compare/2.8.2...2.9.0
[2.9.0...2.9.1]: https://github.com/ergebnis/composer-normalize/compare/2.9.0...2.9.1
[2.9.1...2.10.0]: https://github.com/ergebnis/composer-normalize/compare/2.9.1...2.10.0
[2.10.0...2.11.0]: https://github.com/ergebnis/composer-normalize/compare/2.10.0...2.11.0
[2.11.0...2.12.0]: https://github.com/ergebnis/composer-normalize/compare/2.11.0...2.12.0
[2.12.0...2.12.1]: https://github.com/ergebnis/composer-normalize/compare/2.12.0...2.12.1
[2.12.1...2.12.2]: https://github.com/ergebnis/composer-normalize/compare/2.12.1...2.12.2
[2.12.2...2.13.0]: https://github.com/ergebnis/composer-normalize/compare/2.12.2...2.13.0
[2.13.0...2.13.1]: https://github.com/ergebnis/composer-normalize/compare/2.13.0...2.13.1
[2.13.1...2.13.2]: https://github.com/ergebnis/composer-normalize/compare/2.13.1...2.13.2
[2.13.2...2.13.3]: https://github.com/ergebnis/composer-normalize/compare/2.13.2...2.13.3
[2.13.3...2.13.4]: https://github.com/ergebnis/composer-normalize/compare/2.13.3...2.13.4
[2.13.4...2.14.0]: https://github.com/ergebnis/composer-normalize/compare/2.13.4...2.14.0
[2.14.0...2.15.0]: https://github.com/ergebnis/composer-normalize/compare/2.14.0...2.15.0
[2.15.0...2.16.0]: https://github.com/ergebnis/composer-normalize/compare/2.15.0...2.16.0
[2.16.0...2.17.0]: https://github.com/ergebnis/composer-normalize/compare/2.16.0...2.17.0
[2.17.0...2.18.0]: https://github.com/ergebnis/composer-normalize/compare/2.17.0...2.18.0
[2.18.0...2.19.0]: https://github.com/ergebnis/composer-normalize/compare/2.18.0...2.19.0
[2.19.0...2.20.0]: https://github.com/ergebnis/composer-normalize/compare/2.19.0...2.20.0
[2.20.0...2.21.0]: https://github.com/ergebnis/composer-normalize/compare/2.20.0...2.21.0
[2.21.0...2.22.0]: https://github.com/ergebnis/composer-normalize/compare/2.21.0...2.22.0
[2.22.0...2.23.0]: https://github.com/ergebnis/composer-normalize/compare/2.22.0...2.23.0
[2.23.0...2.23.1]: https://github.com/ergebnis/composer-normalize/compare/2.23.0...2.23.1
[2.23.1...2.24.0]: https://github.com/ergebnis/composer-normalize/compare/2.23.1...2.24.0
[2.24.0...2.24.1]: https://github.com/ergebnis/composer-normalize/compare/2.24.0...2.24.1
[2.24.1...2.25.0]: https://github.com/ergebnis/composer-normalize/compare/2.24.1...2.25.0
[2.25.0...2.25.1]: https://github.com/ergebnis/composer-normalize/compare/2.25.0...2.25.1
[2.25.1...2.25.2]: https://github.com/ergebnis/composer-normalize/compare/2.25.1...2.25.2
[2.25.2...2.26.0]: https://github.com/ergebnis/composer-normalize/compare/2.25.2...2.26.0
[2.26.0...2.27.0]: https://github.com/ergebnis/composer-normalize/compare/2.26.0...2.27.0
[2.27.0...2.28.0]: https://github.com/ergebnis/composer-normalize/compare/2.27.0...2.28.0
[2.28.0...2.28.1]: https://github.com/ergebnis/composer-normalize/compare/2.28.0...2.28.1
[2.28.1...2.28.2]: https://github.com/ergebnis/composer-normalize/compare/2.28.1...2.28.2
[2.28.2...2.28.3]: https://github.com/ergebnis/composer-normalize/compare/2.28.2...2.38.3
[2.28.3...2.29.0]: https://github.com/ergebnis/composer-normalize/compare/2.28.3...2.29.0
[2.29.0...2.30.0]: https://github.com/ergebnis/composer-normalize/compare/2.29.0...2.30.0
[2.30.0...2.30.1]: https://github.com/ergebnis/composer-normalize/compare/2.30.0...2.30.1
[2.30.1...2.30.2]: https://github.com/ergebnis/composer-normalize/compare/2.30.1...2.30.2
[2.30.2...2.31.0]: https://github.com/ergebnis/composer-normalize/compare/2.30.2...2.31.0
[2.31.0...2.32.0]: https://github.com/ergebnis/composer-normalize/compare/2.31.0...2.32.0
[2.32.0...2.33.0]: https://github.com/ergebnis/composer-normalize/compare/2.32.0...2.33.0
[2.33.0...2.34.0]: https://github.com/ergebnis/composer-normalize/compare/2.33.0...2.34.0
[2.34.0...2.35.0]: https://github.com/ergebnis/composer-normalize/compare/2.34.0...2.35.0
[2.35.0...2.36.0]: https://github.com/ergebnis/composer-normalize/compare/2.35.0...2.36.0
[2.36.0...2.37.0]: https://github.com/ergebnis/composer-normalize/compare/2.36.0...2.37.0
[2.37.0...2.38.0]: https://github.com/ergebnis/composer-normalize/compare/2.37.0...2.38.0
[2.38.0...2.39.0]: https://github.com/ergebnis/composer-normalize/compare/2.38.0...2.39.0
[2.39.0...2.40.0]: https://github.com/ergebnis/composer-normalize/compare/2.39.0...2.40.0
[2.40.0...2.41.0]: https://github.com/ergebnis/composer-normalize/compare/2.40.0...2.41.0
[2.41.0...2.41.1]: https://github.com/ergebnis/composer-normalize/compare/2.41.0...2.41.1
[2.41.1...2.42.0]: https://github.com/ergebnis/composer-normalize/compare/2.41.1...2.42.0
[2.42.0...main]: https://github.com/ergebnis/composer-normalize/compare/2.42.0...main

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
[#292]: https://github.com/ergebnis/composer-normalize/pull/292
[#297]: https://github.com/ergebnis/composer-normalize/pull/297
[#301]: https://github.com/ergebnis/composer-normalize/pull/301
[#303]: https://github.com/ergebnis/composer-normalize/pull/303
[#316]: https://github.com/ergebnis/composer-normalize/pull/316
[#322]: https://github.com/ergebnis/composer-normalize/pull/322
[#354]: https://github.com/ergebnis/composer-normalize/pull/354
[#364]: https://github.com/ergebnis/composer-normalize/pull/364
[#374]: https://github.com/ergebnis/composer-normalize/pull/374
[#379]: https://github.com/ergebnis/composer-normalize/pull/379
[#380]: https://github.com/ergebnis/composer-normalize/pull/380
[#406]: https://github.com/ergebnis/composer-normalize/pull/406
[#412]: https://github.com/ergebnis/composer-normalize/pull/412
[#416]: https://github.com/ergebnis/composer-normalize/pull/416
[#420]: https://github.com/ergebnis/composer-normalize/pull/420
[#422]: https://github.com/ergebnis/composer-normalize/pull/422
[#424]: https://github.com/ergebnis/composer-normalize/pull/424
[#465]: https://github.com/ergebnis/composer-normalize/pull/465
[#481]: https://github.com/ergebnis/composer-normalize/pull/481
[#484]: https://github.com/ergebnis/composer-normalize/pull/484
[#485]: https://github.com/ergebnis/composer-normalize/pull/485
[#487]: https://github.com/ergebnis/composer-normalize/pull/487
[#512]: https://github.com/ergebnis/composer-normalize/pull/512
[#515]: https://github.com/ergebnis/composer-normalize/pull/515
[#526]: https://github.com/ergebnis/composer-normalize/pull/526
[#529]: https://github.com/ergebnis/composer-normalize/pull/529
[#554]: https://github.com/ergebnis/composer-normalize/pull/554
[#572]: https://github.com/ergebnis/composer-normalize/pull/572
[#582]: https://github.com/ergebnis/composer-normalize/pull/582
[#596]: https://github.com/ergebnis/composer-normalize/pull/596
[#597]: https://github.com/ergebnis/composer-normalize/pull/597
[#608]: https://github.com/ergebnis/composer-normalize/pull/608
[#615]: https://github.com/ergebnis/composer-normalize/pull/615
[#634]: https://github.com/ergebnis/composer-normalize/pull/634
[#640]: https://github.com/ergebnis/composer-normalize/pull/640
[#641]: https://github.com/ergebnis/composer-normalize/pull/641
[#643]: https://github.com/ergebnis/composer-normalize/pull/643
[#644]: https://github.com/ergebnis/composer-normalize/pull/644
[#646]: https://github.com/ergebnis/composer-normalize/pull/646
[#647]: https://github.com/ergebnis/composer-normalize/pull/647
[#707]: https://github.com/ergebnis/composer-normalize/pull/707
[#743]: https://github.com/ergebnis/composer-normalize/pull/743
[#744]: https://github.com/ergebnis/composer-normalize/pull/744
[#750]: https://github.com/ergebnis/composer-normalize/pull/750
[#754]: https://github.com/ergebnis/composer-normalize/pull/754
[#804]: https://github.com/ergebnis/composer-normalize/pull/804
[#807]: https://github.com/ergebnis/composer-normalize/pull/807
[#816]: https://github.com/ergebnis/composer-normalize/pull/816
[#825]: https://github.com/ergebnis/composer-normalize/pull/825
[#827]: https://github.com/ergebnis/composer-normalize/pull/827
[#829]: https://github.com/ergebnis/composer-normalize/pull/829
[#842]: https://github.com/ergebnis/composer-normalize/pull/842
[#845]: https://github.com/ergebnis/composer-normalize/pull/845
[#852]: https://github.com/ergebnis/composer-normalize/pull/852
[#858]: https://github.com/ergebnis/composer-normalize/pull/858
[#863]: https://github.com/ergebnis/composer-normalize/pull/863
[#864]: https://github.com/ergebnis/composer-normalize/pull/864
[#871]: https://github.com/ergebnis/composer-normalize/pull/871
[#875]: https://github.com/ergebnis/composer-normalize/pull/875
[#877]: https://github.com/ergebnis/composer-normalize/pull/877
[#899]: https://github.com/ergebnis/composer-normalize/pull/899
[#904]: https://github.com/ergebnis/composer-normalize/pull/904
[#905]: https://github.com/ergebnis/composer-normalize/pull/905
[#912]: https://github.com/ergebnis/composer-normalize/pull/912
[#913]: https://github.com/ergebnis/composer-normalize/pull/913
[#915]: https://github.com/ergebnis/composer-normalize/pull/915
[#916]: https://github.com/ergebnis/composer-normalize/pull/916
[#920]: https://github.com/ergebnis/composer-normalize/pull/920
[#922]: https://github.com/ergebnis/composer-normalize/pull/922
[#923]: https://github.com/ergebnis/composer-normalize/pull/923
[#930]: https://github.com/ergebnis/composer-normalize/pull/930
[#933]: https://github.com/ergebnis/composer-normalize/pull/933
[#938]: https://github.com/ergebnis/composer-normalize/pull/938
[#941]: https://github.com/ergebnis/composer-normalize/pull/941
[#942]: https://github.com/ergebnis/composer-normalize/pull/942
[#959]: https://github.com/ergebnis/composer-normalize/pull/959
[#998]: https://github.com/ergebnis/composer-normalize/pull/998
[#1004]: https://github.com/ergebnis/composer-normalize/pull/1004
[#1008]: https://github.com/ergebnis/composer-normalize/pull/1008
[#1020]: https://github.com/ergebnis/composer-normalize/pull/1020
[#1056]: https://github.com/ergebnis/composer-normalize/pull/1056
[#1060]: https://github.com/ergebnis/composer-normalize/pull/1060
[#1062]: https://github.com/ergebnis/composer-normalize/pull/1062
[#1070]: https://github.com/ergebnis/composer-normalize/pull/1070
[#1094]: https://github.com/ergebnis/composer-normalize/pull/1094
[#1095]: https://github.com/ergebnis/composer-normalize/pull/1095
[#1118]: https://github.com/ergebnis/composer-normalize/pull/1118
[#1125]: https://github.com/ergebnis/composer-normalize/pull/1125
[#1127]: https://github.com/ergebnis/composer-normalize/pull/1127
[#1136]: https://github.com/ergebnis/composer-normalize/pull/1136
[#1141]: https://github.com/ergebnis/composer-normalize/pull/1141
[#1155]: https://github.com/ergebnis/composer-normalize/pull/1155
[#1158]: https://github.com/ergebnis/composer-normalize/pull/1158
[#1170]: https://github.com/ergebnis/composer-normalize/pull/1170
[#1171]: https://github.com/ergebnis/composer-normalize/pull/1171
[#1188]: https://github.com/ergebnis/composer-normalize/pull/1188
[#1189]: https://github.com/ergebnis/composer-normalize/pull/1189
[#1191]: https://github.com/ergebnis/composer-normalize/pull/1191
[#1192]: https://github.com/ergebnis/composer-normalize/pull/1192
[#1195]: https://github.com/ergebnis/composer-normalize/pull/1195
[#1204]: https://github.com/ergebnis/composer-normalize/pull/1204
[#1234]: https://github.com/ergebnis/composer-normalize/pull/1234
[#1237]: https://github.com/ergebnis/composer-normalize/pull/1237
[#1241]: https://github.com/ergebnis/composer-normalize/pull/1241
[#1243]: https://github.com/ergebnis/composer-normalize/pull/1243
[#1273]: https://github.com/ergebnis/composer-normalize/pull/1273
[#1275]: https://github.com/ergebnis/composer-normalize/pull/1275
[#1277]: https://github.com/ergebnis/composer-normalize/pull/1277
[#1278]: https://github.com/ergebnis/composer-normalize/pull/1278
[#1279]: https://github.com/ergebnis/composer-normalize/pull/1279

[@core23]: https://github.com/core23
[@dependabot]: https://github.com/dependabot
[@ergebnis-bot]: https://github.com/ergebnis-bot
[@mxr576]: https://github.com/mxr576
[@ergebnis]: https://github.com/ergebnis
[@localheinz]: https://github.com/localheinz
[@svenluijten]: https://github.com/svenluijten
[@TravisCarden]: https://github.com/TravisCarden
