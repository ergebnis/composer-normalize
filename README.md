# composer-normalize

[![Build Status](https://travis-ci.com/localheinz/composer-normalize.svg?branch=master)](https://travis-ci.com/localheinz/composer-normalize)
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
[`localheinz/composer-json-normalizer`](https://github.com/localheinz/composer-json-normalizer#normalizers) for a full explanation, or take a look at the [examples](https://github.com/localheinz/composer-normalize#examples)

### Arguments

* `file`: Path to composer.json file (optional, defaults to `composer.json` in working directory)

### Options

* `--dry-run`: Show the results of normalizing, but do not modify any files
* `--indent-size`: Indent size (an integer greater than 0); should be used with the `--indent-style` option
* `--indent-style`: Indent style (one of "space", "tab"); should be used with the `--indent-size` option
* `--no-update-lock`: Do not update lock file if it exists

### Continuous Integration

If you want to run this in continuous integration services, use the `--dry-run` option.

```
$ composer normalize --dry-run
```

In case `composer.json` is not normalized (or `composer.lock` is not up-to-date), the command will
fail with an exit code of `1` and show a diff.

## Examples

### `laravel/laravel`

Running

```
$ composer normalize
```

against https://github.com/laravel/laravel/blob/v5.6.12/composer.json yields the following diff:

```diff
diff --git a/composer.json b/composer.json
index 65bf8b4f..507ab39c 100644
--- a/composer.json
+++ b/composer.json
@@ -1,9 +1,12 @@
 {
     "name": "laravel/laravel",
+    "type": "project",
     "description": "The Laravel Framework.",
-    "keywords": ["framework", "laravel"],
+    "keywords": [
+        "framework",
+        "laravel"
+    ],
     "license": "MIT",
-    "type": "project",
     "require": {
         "php": "^7.1.3",
         "fideloper/proxy": "^4.0",
@@ -17,43 +20,42 @@
         "nunomaduro/collision": "^2.0",
         "phpunit/phpunit": "^7.0"
     },
+    "config": {
+        "optimize-autoloader": true,
+        "preferred-install": "dist",
+        "sort-packages": true
+    },
+    "extra": {
+        "laravel": {
+            "dont-discover": []
+        }
+    },
     "autoload": {
+        "psr-4": {
+            "App\\": "app/"
+        },
         "classmap": [
             "database/seeds",
             "database/factories"
-        ],
-        "psr-4": {
-            "App\\": "app/"
-        }
+        ]
     },
     "autoload-dev": {
         "psr-4": {
             "Tests\\": "tests/"
         }
     },
-    "extra": {
-        "laravel": {
-            "dont-discover": [
-            ]
-        }
-    },
+    "minimum-stability": "dev",
+    "prefer-stable": true,
     "scripts": {
+        "post-autoload-dump": [
+            "Illuminate\\Foundation\\ComposerScripts::postAutoloadDump",
+            "@php artisan package:discover"
+        ],
         "post-root-package-install": [
             "@php -r \"file_exists('.env') || copy('.env.example', '.env');\""
         ],
         "post-create-project-cmd": [
             "@php artisan key:generate"
-        ],
-        "post-autoload-dump": [
-            "Illuminate\\Foundation\\ComposerScripts::postAutoloadDump",
-            "@php artisan package:discover"
         ]
-    },
-    "config": {
-        "preferred-install": "dist",
-        "sort-packages": true,
-        "optimize-autoloader": true
-    },
-    "minimum-stability": "dev",
-    "prefer-stable": true
+    }
 }
```

### `symfony/symfony`

Running

```
$ composer normalize
```

against https://github.com/symfony/symfony/blob/v4.1.7/composer.json yields the following diff:

```diff
diff --git a/composer.json b/composer.json
index f861cbca31..b36000853a 100644
--- a/composer.json
+++ b/composer.json
@@ -2,7 +2,9 @@
     "name": "symfony/symfony",
     "type": "library",
     "description": "The Symfony PHP framework",
-    "keywords": ["framework"],
+    "keywords": [
+        "framework"
+    ],
     "homepage": "https://symfony.com",
     "license": "MIT",
     "authors": [
@@ -20,7 +22,6 @@
         "ext-xml": "*",
         "doctrine/common": "~2.4",
         "fig/link-util": "^1.0",
-        "twig/twig": "^1.35|^2.4.4",
         "psr/cache": "~1.0",
         "psr/container": "^1.0",
         "psr/link": "^1.0",
@@ -29,7 +30,8 @@
         "symfony/polyfill-ctype": "~1.8",
         "symfony/polyfill-intl-icu": "~1.0",
         "symfony/polyfill-mbstring": "~1.0",
-        "symfony/polyfill-php72": "~1.5"
+        "symfony/polyfill-php72": "~1.5",
+        "twig/twig": "^1.35 || ^2.4.4"
     },
     "replace": {
         "symfony/asset": "self.version",
@@ -38,9 +40,9 @@
         "symfony/config": "self.version",
         "symfony/console": "self.version",
         "symfony/css-selector": "self.version",
-        "symfony/dependency-injection": "self.version",
         "symfony/debug": "self.version",
         "symfony/debug-bundle": "self.version",
+        "symfony/dependency-injection": "self.version",
         "symfony/doctrine-bridge": "self.version",
         "symfony/dom-crawler": "self.version",
         "symfony/dotenv": "self.version",
@@ -65,11 +67,11 @@
         "symfony/proxy-manager-bridge": "self.version",
         "symfony/routing": "self.version",
         "symfony/security": "self.version",
+        "symfony/security-bundle": "self.version",
         "symfony/security-core": "self.version",
         "symfony/security-csrf": "self.version",
         "symfony/security-guard": "self.version",
         "symfony/security-http": "self.version",
-        "symfony/security-bundle": "self.version",
         "symfony/serializer": "self.version",
         "symfony/stopwatch": "self.version",
         "symfony/templating": "self.version",
@@ -84,32 +86,37 @@
         "symfony/workflow": "self.version",
         "symfony/yaml": "self.version"
     },
+    "conflict": {
+        "phpdocumentor/reflection-docblock": "<3.0 || >=3.2.0,<3.2.2",
+        "phpdocumentor/type-resolver": "<0.3.0",
+        "phpunit/phpunit": "<5.4.3"
+    },
+    "provide": {
+        "psr/cache-implementation": "1.0",
+        "psr/container-implementation": "1.0",
+        "psr/log-implementation": "1.0",
+        "psr/simple-cache-implementation": "1.0"
+    },
     "require-dev": {
         "cache/integration-tests": "dev-master",
         "doctrine/annotations": "~1.0",
         "doctrine/cache": "~1.6",
         "doctrine/data-fixtures": "1.0.*",
         "doctrine/dbal": "~2.4",
-        "doctrine/orm": "~2.4,>=2.4.5",
         "doctrine/doctrine-bundle": "~1.4",
+        "doctrine/orm": "~2.4,>=2.4.5",
+        "egulias/email-validator": "~1.2,>=1.2.8 || ~2.0",
         "monolog/monolog": "~1.11",
-        "ocramius/proxy-manager": "~0.4|~1.0|~2.0",
+        "ocramius/proxy-manager": "~0.4 || ~1.0 || ~2.0",
+        "phpdocumentor/reflection-docblock": "^3.0 || ^4.0",
         "predis/predis": "~1.0",
-        "egulias/email-validator": "~1.2,>=1.2.8|~2.0",
-        "symfony/phpunit-bridge": "~3.4|~4.0",
-        "symfony/security-acl": "~2.8|~3.0",
-        "phpdocumentor/reflection-docblock": "^3.0|^4.0"
+        "symfony/phpunit-bridge": "~3.4 || ~4.0",
+        "symfony/security-acl": "~2.8 || ~3.0"
     },
-    "conflict": {
-        "phpdocumentor/reflection-docblock": "<3.0||>=3.2.0,<3.2.2",
-        "phpdocumentor/type-resolver": "<0.3.0",
-        "phpunit/phpunit": "<5.4.3"
-    },
-    "provide": {
-        "psr/cache-implementation": "1.0",
-        "psr/container-implementation": "1.0",
-        "psr/log-implementation": "1.0",
-        "psr/simple-cache-implementation": "1.0"
+    "extra": {
+        "branch-alias": {
+            "dev-master": "4.1-dev"
+        }
     },
     "autoload": {
         "psr-4": {
@@ -128,12 +135,9 @@
         ]
     },
     "autoload-dev": {
-        "files": [ "src/Symfony/Component/VarDumper/Resources/functions/dump.php" ]
+        "files": [
+            "src/Symfony/Component/VarDumper/Resources/functions/dump.php"
+        ]
     },
-    "minimum-stability": "dev",
-    "extra": {
-        "branch-alias": {
-            "dev-master": "4.1-dev"
-        }
-    }
+    "minimum-stability": "dev"
 }
```

### `zendframework/zend-expressive`

Running

```
$ composer normalize
```

against https://github.com/zendframework/zend-expressive/blob/3.2.1/composer.json yields the following diff:

```diff
diff --git a/composer.json b/composer.json
index 478ab18a..773be7fa 100644
--- a/composer.json
+++ b/composer.json
@@ -1,7 +1,6 @@
 {
     "name": "zendframework/zend-expressive",
     "description": "PSR-15 Middleware Microframework",
-    "license": "BSD-3-Clause",
     "keywords": [
         "http",
         "middleware",
@@ -14,14 +13,7 @@
         "zendframework",
         "zend-expressive"
     ],
-    "support": {
-        "docs": "https://docs.zendframework.com/zend-expressive/",
-        "issues": "https://github.com/zendframework/zend-expressive/issues",
-        "source": "https://github.com/zendframework/zend-expressive",
-        "rss": "https://github.com/zendframework/zend-expressive/releases.atom",
-        "slack": "https://zendframework-slack.herokuapp.com",
-        "forum": "https://discourse.zendframework.com/c/questions/expressive"
-    },
+    "license": "BSD-3-Clause",
     "require": {
         "php": "^7.1",
         "fig/http-message-util": "^1.1.2",
         "http",
         "middleware",
@@ -14,14 +13,7 @@
         "zendframework",
         "zend-expressive"
     ],
-    "support": {
-        "docs": "https://docs.zendframework.com/zend-expressive/",
-        "issues": "https://github.com/zendframework/zend-expressive/issues",
-        "source": "https://github.com/zendframework/zend-expressive",
-        "rss": "https://github.com/zendframework/zend-expressive/releases.atom",
-        "slack": "https://zendframework-slack.herokuapp.com",
-        "forum": "https://discourse.zendframework.com/c/questions/expressive"
-    },
+    "license": "BSD-3-Clause",
     "require": {
         "php": "^7.1",
         "fig/http-message-util": "^1.1.2",
@@ -33,6 +25,10 @@
         "zendframework/zend-httphandlerrunner": "^1.0.1",
         "zendframework/zend-stratigility": "^3.0"
     },
+    "conflict": {
+        "container-interop/container-interop": "<1.2.0",
+        "zendframework/zend-diactoros": "<1.7.1"
+    },
     "require-dev": {
         "filp/whoops": "^1.1.10 || ^2.1.13",
         "malukenho/docheader": "^0.1.6",
@@ -47,10 +43,6 @@
         "zendframework/zend-expressive-zendrouter": "^3.0",
         "zendframework/zend-servicemanager": "^2.7.8 || ^3.3"
     },
-    "conflict": {
-        "container-interop/container-interop": "<1.2.0",
-        "zendframework/zend-diactoros": "<1.7.1"
-    },
     "suggest": {
         "filp/whoops": "^2.1 to use the Whoops error handler",
         "psr/http-message-implementation": "Please install a psr/http-message-implementation to consume Expressive; e.g., zendframework/zend-diactoros",
@@ -60,19 +52,6 @@
         "zendframework/zend-pimple-config": "^1.0 to use Pimple for dependency injection container",
         "zendframework/zend-servicemanager": "^3.3 to use zend-servicemanager for dependency injection"
     },
-    "autoload": {
-        "files": [
-            "src/constants.php"
-        ],
-        "psr-4": {
-            "Zend\\Expressive\\": "src/"
-        }
-    },
-    "autoload-dev": {
-        "psr-4": {
-            "ZendTest\\Expressive\\": "test/"
-        }
-    },
     "config": {
         "sort-packages": true
     },
@@ -85,6 +64,19 @@
             "config-provider": "Zend\\Expressive\\ConfigProvider"
         }
     },
+    "autoload": {
+        "psr-4": {
+            "Zend\\Expressive\\": "src/"
+        },
+        "files": [
+            "src/constants.php"
+        ]
+    },
+    "autoload-dev": {
+        "psr-4": {
+            "ZendTest\\Expressive\\": "test/"
+        }
+    },
     "bin": [
         "bin/expressive-tooling"
     ],
@@ -96,9 +88,17 @@
         ],
         "cs-check": "phpcs",
         "cs-fix": "phpcbf",
+        "license-check": "docheader check src/ test/",
         "phpstan": "phpstan analyze -l max -c phpstan.neon ./src",
         "test": "phpunit --colors=always",
-        "test-coverage": "phpunit --colors=always --coverage-clover clover.xml",
-        "license-check": "docheader check src/ test/"
+        "test-coverage": "phpunit --colors=always --coverage-clover clover.xml"
+    },
+    "support": {
+        "issues": "https://github.com/zendframework/zend-expressive/issues",
+        "forum": "https://discourse.zendframework.com/c/questions/expressive",
+        "source": "https://github.com/zendframework/zend-expressive",
+        "docs": "https://docs.zendframework.com/zend-expressive/",
+        "rss": "https://github.com/zendframework/zend-expressive/releases.atom",
+        "slack": "https://zendframework-slack.herokuapp.com"
     }
 }
```

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
