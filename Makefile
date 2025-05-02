install: composer.phar
	./composer.phar install

update: composer.phar
	./composer.phar self-update
	./composer.phar update

test: composer.lock
	./vendor/bin/phpunit

cs: composer.lock
	./vendor/bin/phpcs

stan: composer.lock
	./vendor/bin/phpstan analyse --memory-limit 256M

coverage: composer.lock
	XDEBUG_MODE=coverage ./vendor/bin/phpunit --coverage-clover build/logs/clover.xml
	./vendor/bin/php-coveralls -v

coverage-summary: composer.lock
	XDEBUG_MODE=coverage ./vendor/bin/phpunit --coverage-html=coverage

composer.phar:
	curl -s http://getcomposer.org/installer | php

composer.lock: composer.phar
	./composer.phar --no-interaction install

vendor/bin/phpunit: install
