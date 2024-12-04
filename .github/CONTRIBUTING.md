# Contributing

We use [GitHub Actions](https://github.com/features/actions) as a continuous integration system.

For details, take a look at the following workflow configuration files:

- [`workflows/integrate.yaml`](workflows/integrate.yaml)
- [`workflows/merge.yaml`](workflows/merge.yaml)
- [`workflows/release.yaml`](workflows/release.yaml)
- [`workflows/renew.yaml`](workflows/renew.yaml)
- [`workflows/triage.yaml`](workflows/triage.yaml)
- [`workflows/update.yaml`](workflows/update.yaml)

## Coding Standards

We use [`ergebnis/composer-normalize`](https://github.com/ergebnis/composer-normalize) to normalize `composer.json`.

We use [`yamllint`](https://github.com/adrienverge/yamllint) to enforce coding standards in YAML files.

If you do not have `yamllint` installed yet, run

```sh
brew install yamllint
```

to install `yamllint`.

We use [`friendsofphp/php-cs-fixer`](https://github.com/FriendsOfPHP/PHP-CS-Fixer) to enforce coding standards in PHP files.

Run

```sh
make coding-standards
```

to automatically fix coding standard violations.

## Dependency Analysis

We use [`maglnet/composer-require-checker`](https://github.com/maglnet/ComposerRequireChecker) to prevent the use of unknown symbols in production code.

Run

```sh
make dependency-analysis
```

to run a dependency analysis.

## Mutation Tests

We use [`infection/infection`](https://github.com/infection/infection) to ensure a minimum quality of the tests.

Enable `Xdebug` and run

```sh
make mutation-tests
```

to run mutation tests.

## Refactoring

We use [`rector/rector`](https://github.com/rectorphp/rector) to automatically refactor code.

Run

```sh
make refactoring
```

to automatically refactor code.

## Security Analysis

We use [`composer`](https://github.com/composer/composer) to run a security analysis.

Run

```sh
make security-analysis
```

to run a security analysis.

## Static Code Analysis

We use [`phpstan/phpstan`](https://github.com/phpstan/phpstan) to statically analyze the code.

Run

```sh
make static-code-analysis
```

to run a static code analysis.

We also use the baseline feature of [`phpstan/phpstan`](https://phpstan.org/user-guide/baseline).

Run

```sh
make static-code-analysis-baseline
```

to regenerate the baseline in [`../phpstan-baseline.neon`](../phpstan-baseline.neon).

:exclamation: Ideally, the baseline should shrink over time.

## Tests

We use [`phpunit/phpunit`](https://github.com/sebastianbergmann/phpunit) to drive the development.

Run

```sh
make tests
```

to run all the tests.

## Extra lazy?

Run

```sh
make
```

to automatically refactor code, enforce coding standards, run a static code analysis, and run tests!

## Help

:bulb: Run

```sh
make help
```

to display a list of available targets with corresponding descriptions.
