.PHONY: coverage cs it stan test

it: cs stan test

coverage: vendor
	mkdir -p .build/phpunit
	vendor/bin/phpunit --configuration=test/Integration/phpunit.xml --dump-xdebug-filter=.build/phpunit/xdebug-filter.php
	vendor/bin/phpunit --configuration=test/Integration/phpunit.xml --coverage-text --prepend=.build/phpunit/xdebug-filter.php

cs: vendor
	mkdir -p .build/php-cs-fixer
	vendor/bin/php-cs-fixer fix --config=.php_cs --diff --verbose

stan: vendor
	mkdir -p .build/phpstan
	vendor/bin/phpstan analyse --configuration=phpstan.neon

test: vendor
	mkdir -p .build/phpunit
	vendor/bin/phpunit --configuration=test/Integration/phpunit.xml

vendor: composer.json composer.lock
	composer validate
	composer install
