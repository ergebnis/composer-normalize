# composer-normalize

[![Integrate](https://github.com/ergebnis/composer-normalize/workflows/Integrate/badge.svg)](https://github.com/ergebnis/composer-normalize/actions)
[![Merge](https://github.com/ergebnis/composer-normalize/workflows/Merge/badge.svg)](https://github.com/ergebnis/composer-normalize/actions)
[![Release](https://github.com/ergebnis/composer-normalize/workflows/Release/badge.svg)](https://github.com/ergebnis/composer-normalize/actions)
[![Renew](https://github.com/ergebnis/composer-normalize/workflows/Renew/badge.svg)](https://github.com/ergebnis/composer-normalize/actions)
[![Update](https://github.com/ergebnis/composer-normalize/workflows/Update/badge.svg)](https://github.com/ergebnis/composer-normalize/actions)

[![Code Coverage](https://codecov.io/gh/ergebnis/composer-normalize/branch/main/graph/badge.svg)](https://codecov.io/gh/ergebnis/composer-normalize)

[![Latest Stable Version](https://poser.pugx.org/ergebnis/composer-normalize/v/stable)](https://packagist.org/packages/ergebnis/composer-normalize)
[![Total Downloads](https://poser.pugx.org/ergebnis/composer-normalize/downloads)](https://packagist.org/packages/ergebnis/composer-normalize)
[![Monthly Downloads](http://poser.pugx.org/ergebnis/composer-normalize/d/monthly)](https://packagist.org/packages/ergebnis/composer-normalize)

This project provides a [`composer`](https://getcomposer.org) plugin for normalizing [`composer.json`](https://getcomposer.org/doc/04-schema.md).

[![Hmm, kinda cool I guess](https://user-images.githubusercontent.com/605483/150120621-1eb65e19-b924-481c-a9e5-e762f1f3cfc9.png)](https://github.com/laravel/laravel/pull/4856#issuecomment-439705243)

## Why

When it comes to formatting `composer.json`, you have the following options:

- you can format it manually (and request changes when contributors format it differently)
- you can stop caring
- or you can use `ergebnis/composer-normalize`

`ergebnis/composer-normalize` normalizes `composer.json`, so you don't have to.

:bulb: If you want to find out more, take a look at the [examples](#examples) and read this [blog post](https://localheinz.com/blog/2018/01/15/normalizing-composer.json/).

## Installation

### Composer

Run

```sh
composer require --dev ergebnis/composer-normalize
```

to install `ergebnis/composer-normalize` as a composer plugin.

Run

```shell
composer config allow-plugins.ergebnis/composer-normalize true
```

to allow `ergebnis/composer-normalize` to run as a composer plugin.

:bulb: The `allow-plugins` has been added to `composer/composer` to add an extra layer of security.

For reference, see

- https://github.com/composer/composer/pull/10314
- https://getcomposer.org/doc/06-config.md#allow-plugins

### Phar

Head over to http://github.com/ergebnis/composer-normalize/releases/latest and download the latest `composer-normalize.phar`.

Run

```sh
chmod +x composer-normalize.phar
```

to make the downloaded `composer-normalize.phar` executable.

### Phive

Run

```sh
phive install ergebnis/composer-normalize
```

to install `ergebnis/composer-normalize` with [PHIVE](https://phar.io).

## Usage

### Composer

Run

```sh
composer normalize
```

to normalize `composer.json` in the working directory.

### Phar

Run

```sh
./composer-normalize.phar
```

to normalize `composer.json` in the working directory.

### Phive

Run

```sh
./tools/composer-normalize
```

to normalize `composer.json` in the working directory.

### Details

The `NormalizeCommand` provided by the `NormalizePlugin` within this package will

- determine whether a `composer.json` exists
- determine whether a `composer.lock` exists, and if so, whether it is up to date (unless the `--no-check-lock` option is used)
- use [`Ergebnis\Json\Normalizer\Vendor\Composer\ComposerJsonNormalizer`](https://github.com/ergebnis/json-normalizer#vendorcomposercomposerjsonnormalizer) to normalize the content of `composer.json`
- format the normalized content (either as sniffed, or as specified using the `--indent-size` and `--indent-style` options)
- write the normalized and formatted content of `composer.json` back to the file
- update the hash in `composer.lock` if it exists and if an update is necessary

### Arguments

- `file`: Path to `composer.json` file (optional, defaults to `composer.json` in working directory)

### Options

- `--diff`: Show the results of normalizing
- `--dry-run`: Show the results of normalizing, but do not modify any files
- `--indent-size`: Indent size (an integer greater than 0); should be used with the `--indent-style` option
- `--indent-style`: Indent style (one of "space", "tab"); should be used with the `--indent-size` option
- `--no-check-lock`: Do not check if lock file is up to date
- `--no-update-lock`: Do not update lock file if it exists

As an alternative to specifying the `--indent-size` and `--indent-style` options, you can also use composer [extra](https://getcomposer.org/doc/04-schema.md#extra) to configure these options in `composer.json`:

```json
{
  "extra": {
    "composer-normalize": {
      "indent-size": 2,
      "indent-style": "space"
    }
  }
}
```

:bulb: The configuration provided in composer extra always overrides the configuration provided via command line options.

### Continuous Integration

If you want to run this in continuous integration services, use the `--dry-run` option.

```sh
composer normalize --dry-run
```

In case `composer.json` is not normalized (or `composer.lock` is not up-to-date), the command will
fail with an exit code of `1` and show a diff.

## Examples

### `pestphp/pest`

Running

```sh
composer normalize
```

against https://github.com/pestphp/pest/blob/v0.3.19/composer.json yields the following diff:

```diff
diff --git a/composer.json b/composer.json
index 1cfbf1e..204f20f 100644
--- a/composer.json
+++ b/composer.json
@@ -25,6 +25,32 @@
         "pestphp/pest-plugin-init": "^0.3",
         "phpunit/phpunit": ">= 9.3.7 <= 9.5.0"
     },
+    "require-dev": {
+        "illuminate/console": "^7.16.1",
+        "illuminate/support": "^7.16.1",
+        "laravel/dusk": "^6.9.1",
+        "mockery/mockery": "^1.4.1",
+        "pestphp/pest-dev-tools": "dev-master"
+    },
+    "config": {
+        "preferred-install": "dist",
+        "sort-packages": true
+    },
+    "extra": {
+        "branch-alias": {
+            "dev-master": "0.3.x-dev"
+        },
+        "laravel": {
+            "providers": [
+                "Pest\\Laravel\\PestServiceProvider"
+            ]
+        },
+        "pest": {
+            "plugins": [
+                "Pest\\Plugins\\Version"
+            ]
+        }
+    },
     "autoload": {
         "psr-4": {
             "Pest\\": "src/"
@@ -42,49 +68,23 @@
             "tests/Autoload.php"
         ]
     },
-    "require-dev": {
-        "illuminate/console": "^7.16.1",
-        "illuminate/support": "^7.16.1",
-        "laravel/dusk": "^6.9.1",
-        "mockery/mockery": "^1.4.1",
-        "pestphp/pest-dev-tools": "dev-master"
-    },
     "minimum-stability": "dev",
     "prefer-stable": true,
-    "config": {
-        "sort-packages": true,
-        "preferred-install": "dist"
-    },
     "bin": [
         "bin/pest"
     ],
     "scripts": {
         "lint": "php-cs-fixer fix -v",
-        "test:lint": "php-cs-fixer fix -v --dry-run",
-        "test:types": "phpstan analyse --ansi --memory-limit=0",
-        "test:unit": "php bin/pest --colors=always --exclude-group=integration",
-        "test:integration": "php bin/pest --colors=always --group=integration",
-        "update:snapshots": "REBUILD_SNAPSHOTS=true php bin/pest --colors=always",
         "test": [
             "@test:lint",
             "@test:types",
             "@test:unit",
             "@test:integration"
-        ]
-    },
-    "extra": {
-        "branch-alias": {
-            "dev-master": "0.3.x-dev"
-        },
-        "pest": {
-            "plugins": [
-                "Pest\\Plugins\\Version"
-            ]
-        },
-        "laravel": {
-            "providers": [
-                "Pest\\Laravel\\PestServiceProvider"
-            ]
-        }
+        ],
+        "test:integration": "php bin/pest --colors=always --group=integration",
+        "test:lint": "php-cs-fixer fix -v --dry-run",
+        "test:types": "phpstan analyse --ansi --memory-limit=0",
+        "test:unit": "php bin/pest --colors=always --exclude-group=integration",
+        "update:snapshots": "REBUILD_SNAPSHOTS=true php bin/pest --colors=always"
     }
 }
```

### `phpspec/phpspec`

Running

```sh
composer normalize
```

against https://github.com/phpspec/phpspec/blob/7.0.1/composer.json yields the following diff:

```diff
diff --git a/composer.json b/composer.json
index 90150a37..276a2ecd 100644
--- a/composer.json
+++ b/composer.json
@@ -1,72 +1,73 @@
 {
-    "name":         "phpspec/phpspec",
-    "description":  "Specification-oriented BDD framework for PHP 7.1+",
-    "keywords":     ["BDD", "SpecBDD", "TDD", "spec", "specification", "tests", "testing"],
-    "homepage":     "http://phpspec.net/",
-    "type":         "library",
-    "license":      "MIT",
-    "authors":      [
+    "name": "phpspec/phpspec",
+    "type": "library",
+    "description": "Specification-oriented BDD framework for PHP 7.1+",
+    "keywords": [
+        "BDD",
+        "SpecBDD",
+        "TDD",
+        "spec",
+        "specification",
+        "tests",
+        "testing"
+    ],
+    "homepage": "http://phpspec.net/",
+    "license": "MIT",
+    "authors": [
         {
-            "name":      "Konstantin Kudryashov",
-            "email":     "ever.zet@gmail.com",
-            "homepage":  "http://everzet.com"
+            "name": "Konstantin Kudryashov",
+            "email": "ever.zet@gmail.com",
+            "homepage": "http://everzet.com"
         },
         {
-            "name":      "Marcello Duarte",
-            "homepage":  "http://marcelloduarte.net/"
+            "name": "Marcello Duarte",
+            "homepage": "http://marcelloduarte.net/"
         },
         {
-            "name":      "Ciaran McNulty",
-            "homepage":  "https://ciaranmcnulty.com/"
+            "name": "Ciaran McNulty",
+            "homepage": "https://ciaranmcnulty.com/"
         }
     ],
-
     "require": {
-        "php":                      "^7.3 || 8.0.*",
-        "phpspec/prophecy":         "^1.9",
-        "phpspec/php-diff":         "^1.0.0",
-        "sebastian/exporter":       "^3.0 || ^4.0",
-        "symfony/console":          "^3.4 || ^4.4 || ^5.0",
+        "php": "^7.3 || 8.0.*",
+        "ext-tokenizer": "*",
+        "doctrine/instantiator": "^1.0.5",
+        "phpspec/php-diff": "^1.0.0",
+        "phpspec/prophecy": "^1.9",
+        "sebastian/exporter": "^3.0 || ^4.0",
+        "symfony/console": "^3.4 || ^4.4 || ^5.0",
         "symfony/event-dispatcher": "^3.4 || ^4.4 || ^5.0",
-        "symfony/process":          "^3.4 || ^4.4 || ^5.0",
-        "symfony/finder":           "^3.4 || ^4.4 || ^5.0",
-        "symfony/yaml":             "^3.4 || ^4.4 || ^5.0",
-        "doctrine/instantiator":    "^1.0.5",
-        "ext-tokenizer":            "*"
+        "symfony/finder": "^3.4 || ^4.4 || ^5.0",
+        "symfony/process": "^3.4 || ^4.4 || ^5.0",
+        "symfony/yaml": "^3.4 || ^4.4 || ^5.0"
+    },
+    "conflict": {
+        "sebastian/comparator": "<1.2.4"
     },
-
     "require-dev": {
-        "behat/behat":           "^3.3",
-        "symfony/filesystem":    "^3.4 || ^4.0 || ^5.0",
-        "phpunit/phpunit":       "^8.0 || ^9.0"
+        "behat/behat": "^3.3",
+        "phpunit/phpunit": "^8.0 || ^9.0",
+        "symfony/filesystem": "^3.4 || ^4.0 || ^5.0"
     },
-
     "suggest": {
         "phpspec/nyan-formatters": "Adds Nyan formatters"
     },
-
-    "conflict": {
-        "sebastian/comparator" : "<1.2.4"
+    "extra": {
+        "branch-alias": {
+            "dev-main": "7.0.x-dev"
+        }
     },
-
     "autoload": {
         "psr-0": {
             "PhpSpec": "src/"
         }
     },
-
     "autoload-dev": {
         "psr-0": {
             "spec\\PhpSpec": "."
         }
     },
-
-    "bin": ["bin/phpspec"],
-
-    "extra": {
-        "branch-alias": {
-            "dev-main": "7.0.x-dev"
-        }
-    }
-
+    "bin": [
+        "bin/phpspec"
+    ]
 }
```

### `phpunit/phpunit`

Running

```sh
composer normalize
```

against https://github.com/sebastianbergmann/phpunit/blob/9.5.0/composer.json yields the following diff:

```diff
diff --git a/composer.json b/composer.json
index fd6461fc3..23c3a3596 100644
--- a/composer.json
+++ b/composer.json
@@ -1,7 +1,7 @@
 {
     "name": "phpunit/phpunit",
-    "description": "The PHP Unit Testing framework.",
     "type": "library",
+    "description": "The PHP Unit Testing framework.",
     "keywords": [
         "phpunit",
         "xunit",
@@ -16,10 +16,6 @@
             "role": "lead"
         }
     ],
-    "support": {
-        "issues": "https://github.com/sebastianbergmann/phpunit/issues"
-    },
-    "prefer-stable": true,
     "require": {
         "php": ">=7.3",
         "ext-dom": "*",
@@ -54,20 +50,22 @@
         "ext-PDO": "*",
         "phpspec/prophecy-phpunit": "^2.0.1"
     },
+    "suggest": {
+        "ext-soap": "*",
+        "ext-xdebug": "*"
+    },
     "config": {
+        "optimize-autoloader": true,
         "platform": {
             "php": "7.3.0"
         },
-        "optimize-autoloader": true,
         "sort-packages": true
     },
-    "suggest": {
-        "ext-soap": "*",
-        "ext-xdebug": "*"
+    "extra": {
+        "branch-alias": {
+            "dev-master": "9.5-dev"
+        }
     },
-    "bin": [
-        "phpunit"
-    ],
     "autoload": {
         "classmap": [
             "src/"
@@ -86,9 +84,11 @@
             "tests/_files/NamespaceCoveredFunction.php"
         ]
     },
-    "extra": {
-        "branch-alias": {
-            "dev-master": "9.5-dev"
-        }
+    "prefer-stable": true,
+    "bin": [
+        "phpunit"
+    ],
+    "support": {
+        "issues": "https://github.com/sebastianbergmann/phpunit/issues"
     }
 }
```

## Changelog

The maintainers of this project record notable changes to this project in a [changelog](CHANGELOG.md).

## Contributing

The maintainers of this project suggest following the [contribution guide](.github/CONTRIBUTING.md).

## Code of Conduct

The maintainers of this project ask contributors to follow the [code of conduct](https://github.com/ergebnis/.github/blob/main/CODE_OF_CONDUCT.md).

## General Support Policy

The maintainers of this project provide limited support.

You can support the maintenance of this project by [sponsoring @ergebnis](https://github.com/sponsors/ergebnis).

## PHP Version Support Policy

This project supports PHP versions with [active and security support](https://www.php.net/supported-versions.php).

The maintainers of this project add support for a PHP version following its initial release and drop support for a PHP version when it has reached the end of security support.

## Security Policy

This project has a [security policy](.github/SECURITY.md).

## License

This project uses the [MIT license](LICENSE.md).

## Social

Follow [@localheinz](https://twitter.com/intent/follow?screen_name=localheinz) and [@ergebnis](https://twitter.com/intent/follow?screen_name=ergebnis) on Twitter.
