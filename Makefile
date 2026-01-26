COMPOSER_VERSION:=2.9.4

.PHONY: it
it: refactoring coding-standards security-analysis static-code-analysis tests ## Runs the refactoring, coding-standards, security-analysis, static-code-analysis, and tests targets

.PHONY: code-coverage
code-coverage: vendor ## Collects code coverage from running unit and integration tests with phpunit/phpunit
	vendor/bin/phpunit --configuration=test/phpunit.xml --coverage-text

.PHONY: coding-standards
coding-standards: phive vendor ## Lints YAML files with yamllint, normalizes composer.json with ergebnis/composer-normalize, and fixes code style issues with friendsofphp/php-cs-fixer
	yamllint -c .yamllint.yaml --strict .
	.phive/composer-normalize
	vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.php --diff --show-progress=dots --verbose

.PHONY: dependency-analysis
dependency-analysis: phive vendor ## Runs a dependency analysis with maglnet/composer-require-checker
	.phive/composer-require-checker check --config-file=$(shell pwd)/composer-require-checker.json --verbose

.PHONY: help
help: ## Displays this list of targets with descriptions
	@grep -E '^[a-zA-Z0-9_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}'

.PHONY: phar
phar: phive vendor ## Builds a phar with humbug/box
	.phive/box validate box.json
	composer remove phpstan/extension-installer --dev --no-interaction
	composer require composer/composer:${COMPOSER_VERSION}  --no-interaction --no-progress --update-with-dependencies
	.phive/box compile --config=box.json
	git checkout HEAD -- composer.json composer.lock
	.phive/box info .build/phar/composer-normalize.phar --list
	.build/phar/composer-normalize.phar
	.build/phar/composer-normalize.phar --dry-run composer.json
	.build/phar/composer-normalize.phar --dry-run --no-ansi composer.json

.PHONY: phive
phive: .phive ## Installs dependencies with phive
	PHIVE_HOME=.build/phive phive install --trust-gpg-keys 0xC00543248C87FB13,0x033E5F8D801A2F8D,0x2DF45277AEF09A2F

.PHONY: refactoring
refactoring: vendor ## Runs automated refactoring with rector/rector
	vendor/bin/rector process --config=rector.php

.PHONY: schema
schema: vendor ## Updates the schema
	wget --output-document=resource/schema.json https://getcomposer.org/schema.json
	php bin/laxify-schema.php

.PHONY: security-analysis
security-analysis: vendor ## Runs a security analysis with composer
	composer audit

.PHONY: static-code-analysis
static-code-analysis: vendor ## Runs a static code analysis with phpstan/phpstan
	vendor/bin/phpstan clear-result-cache --configuration=phpstan.neon
	vendor/bin/phpstan --configuration=phpstan.neon --memory-limit=-1

.PHONY: static-code-analysis-baseline
static-code-analysis-baseline: vendor ## Generates a baseline for static code analysis with phpstan/phpstan
	vendor/bin/phpstan clear-result-cache --configuration=phpstan.neon
	vendor/bin/phpstan --allow-empty-baseline --configuration=phpstan.neon --generate-baseline=phpstan-baseline.neon --memory-limit=-1

.PHONY: tests
tests: vendor ## Runs unit and integration tests with phpunit/phpunit
	vendor/bin/phpunit --configuration=test/phpunit.xml --testsuite=unit
	vendor/bin/phpunit --configuration=test/phpunit.xml --testsuite=integration

vendor: composer.json composer.lock
	composer validate --strict
	composer install --no-interaction --no-progress
