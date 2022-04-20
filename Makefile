COMPOSER_VERSION:=2.3.5

.PHONY: it
it: coding-standards static-code-analysis tests ## Runs the coding-standards, static-code-analysis, and tests targets

.PHONY: code-coverage
code-coverage: vendor ## Collects coverage from running integration tests with phpunit/phpunit
	mkdir -p .build/phpunit
	vendor/bin/phpunit --configuration=test/Integration/phpunit.xml --coverage-text

.PHONY: coding-standards
coding-standards: vendor ## Normalizes composer.json with ergebnis/composer-normalize, lints YAML files with yamllint and fixes code style issues with friendsofphp/php-cs-fixer
	.phive/composer-normalize
	yamllint -c .yamllint.yaml --strict .
	mkdir -p .build/php-cs-fixer
	vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.php --diff --verbose

.PHONY: dependency-analysis
dependency-analysis: vendor ## Runs a dependency analysis with maglnet/composer-require-checker
	.phive/composer-require-checker check --config-file=$(shell pwd)/composer-require-checker.json

.PHONY: help
help: ## Displays this list of targets with descriptions
	@grep -E '^[a-zA-Z0-9_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}'

.PHONY: phar
phar: vendor ## Builds a phar with humbug/box
	.phive/box validate box.json
	composer require composer/composer:${COMPOSER_VERSION}  --no-interaction --no-progress --update-with-dependencies
	.phive/box compile --config=box.json
	git checkout HEAD -- composer.json composer.lock
	.phive/box info .build/phar/composer-normalize.phar
	.build/phar/composer-normalize.phar
	.build/phar/composer-normalize.phar --dry-run composer.json

.PHONY: schema
schema: vendor ## Updates the schema
	wget --output-document=resource/schema.json https://getcomposer.org/schema.json
	php bin/laxify-schema.php

.PHONY: static-code-analysis
static-code-analysis: vendor ## Runs a static code analysis with vimeo/psalm
	mkdir -p .build/psalm
	vendor/bin/psalm --config=psalm.xml --diff --show-info=false --stats --threads=4

.PHONY: static-code-analysis-baseline
static-code-analysis-baseline: vendor ## Generates a baseline for static code analysis with vimeo/psalm
	mkdir -p .build/psalm
	vendor/bin/psalm --config=psalm.xml --set-baseline=psalm-baseline.xml

.PHONY: tests
tests: vendor ## Runs unit and integration tests with phpunit/phpunit
	mkdir -p .build/phpunit
	vendor/bin/phpunit --configuration=test/phpunit.xml --testsuite=unit
	vendor/bin/phpunit --configuration=test/phpunit.xml  --testsuite=integration

vendor: composer.json composer.lock
	composer validate --strict
	composer install --no-interaction --no-progress
