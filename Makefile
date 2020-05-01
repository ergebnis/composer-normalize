COMPOSER_VERSION:=1.10.5

.PHONY: it
it: coding-standards static-code-analysis tests ## Runs the coding-standards, static-code-analysis, and tests targets

.PHONY: code-coverage
code-coverage: vendor ## Collects coverage from running integration tests with phpunit/phpunit
	mkdir -p .build/phpunit
	vendor/bin/phpunit --configuration=test/Integration/phpunit.xml --dump-xdebug-filter=.build/phpunit/xdebug-filter.php
	vendor/bin/phpunit --configuration=test/Integration/phpunit.xml --coverage-text --prepend=.build/phpunit/xdebug-filter.php

.PHONY: coding-standards
coding-standards: vendor ## Fixes code style issues with friendsofphp/php-cs-fixer
	yamllint -c .yamllint.yaml --strict .
	mkdir -p .build/php-cs-fixer
	tools/vendor/bin/php-cs-fixer fix --config=.php_cs --diff --diff-format=udiff --verbose

.PHONY: dependency-analysis
dependency-analysis: vendor ## Runs a dependency analysis with maglnet/composer-require-checker
	docker run --interactive --rm --tty --volume ${PWD}:/app webfactory/composer-require-checker:2.1.0 check --config-file=composer-require-checker.json

.PHONY: help
help: ## Displays this list of targets with descriptions
	@grep -E '^[a-zA-Z0-9_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}'

.PHONY: phar
phar: vendor ## Builds a phar with humbug/box
	phar/box.phar validate box.json
	composer require composer/composer:${COMPOSER_VERSION}  --no-interaction --no-progress --no-suggest --update-with-dependencies
	phar/box.phar compile --config=box.json
	git checkout HEAD -- composer.json composer.lock
	phar/box.phar info .build/phar/composer-normalize.phar
	.build/phar/composer-normalize.phar
	.build/phar/composer-normalize.phar --dry-run composer.json

.PHONY: schema
schema: vendor ## Updates the schema
	wget --output-document=resource/schema.json https://getcomposer.org/schema.json
	php bin/laxify-schema.php

.PHONY: static-code-analysis
static-code-analysis: vendor ## Runs a static code analysis with phpstan/phpstan and vimeo/psalm
	mkdir -p .build/phpstan
	vendor/bin/phpstan analyse --configuration=phpstan.neon
	mkdir -p .build/psalm
	tools/vendor/bin/psalm --config=psalm.xml --diff --diff-methods --show-info=false --stats --threads=4

.PHONY: static-code-analysis-baseline
static-code-analysis-baseline: vendor ## Generates a baseline for static code analysis with phpstan/phpstan and vimeo/psalm
	mkdir -p .build/phpstan
	vendor/bin/phpstan analyze --configuration=phpstan.neon --generate-baseline=phpstan-baseline.neon
	mkdir -p .build/psalm
	tools/vendor/bin/psalm --config=psalm.xml --set-baseline=psalm-baseline.xml

.PHONY: tests
tests: vendor ## Runs auto-review, unit, and integration tests with phpunit/phpunit
	mkdir -p .build/phpunit
	vendor/bin/phpunit --configuration=test/AutoReview/phpunit.xml
	vendor/bin/phpunit --configuration=test/Unit/phpunit.xml
	vendor/bin/phpunit --configuration=test/Integration/phpunit.xml

vendor: composer.json composer.lock tools/composer.json tools/composer.lock
	composer validate --strict
	composer install --no-interaction --no-progress --no-suggest
	composer install --no-interaction --no-progress --no-suggest --working-dir=tools
	docker run --interactive --rm --tty --workdir=/app --volume ${PWD}:/app localheinz/composer-normalize-action:0.5.2
