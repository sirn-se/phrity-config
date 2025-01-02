# Default
all: deps-install


# DEPENDENCY MANAGEMENT

# Updates dependencies according to lock file
install: composer.phar
	./composer.phar --no-interaction install

# Updates dependencies according to json file
update: composer.phar
	./composer.phar self-update
	./composer.phar --no-interaction update


# TESTS AND REPORTS

# Code standard check
cs: composer.lock
	./vendor/bin/phpcs

# Run tests
test: composer.lock
	./vendor/bin/phpunit

# Run tests with clover coverage report
coverage: composer.lock
	./vendor/bin/phpunit --coverage-clover build/logs/clover.xml
	./vendor/bin/php-coveralls -v

# Run static analysis
stan: composer.lock
	./vendor/bin/phpstan analyse


# INITIAL INSTALL

# Ensures composer is installed
composer.phar:
	curl -sS https://getcomposer.org/installer | php

# Ensures composer is installed and dependencies loaded
composer.lock: composer.phar
	./composer.phar --no-interaction install