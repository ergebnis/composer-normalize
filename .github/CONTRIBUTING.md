# CONTRIBUTING

We're using [Travis CI](https://travis-ci.com) as a continuous integration system.

For details, see [`.travis.yml`](../.travis.yml).

## Coding Standards

We are using [`friendsofphp/php-cs-fixer`](https://github.com/FriendsOfPHP/PHP-CS-Fixer) to enforce coding standards.

Run

```
$ make cs
```

to automatically fix coding standard violations.

## Static Code Analysis

We are using [`phpstan/phpstan`](https://github.com/phpstan/phpstan) to statically analyze the code.

Run

```
$ make stan
```

to run a static code analysis.

## Tests

We're using [`phpunit/phpunit`](https://github.com/sebastianbergmann/phpunit) to drive the development.

Run

```
$ make test
```

to run all the tests.

## Extra lazy?

Run

```
$ make
```

to enforce coding standards, perform a static code analysis, and run tests!
