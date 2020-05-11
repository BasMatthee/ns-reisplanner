all: vendor node_modules assets

test: coding-standards unit-tests integration-tests security-tests

coding-standards:
	vendor/bin/phpcs -n -p --colors

unit-tests:
	vendor/bin/phpunit --testsuite unit

integration-tests:
	APP_ENV=test bin/console doctrine:schema:drop --quiet --force --full-database --no-interaction
	APP_ENV=test bin/console doctrine:migrations:migrate --quiet --no-interaction
	vendor/bin/phpunit --testsuite integration

security-tests: vendor
	vendor/bin/security-checker security:check

vendor: composer.json composer.lock
	composer install --no-interaction

node_modules: package.json
	npm install

assets: assets/* assets/*/* node_modules vendor
	./node_modules/.bin/encore dev

clean:
	rm -rf vendor/ node_modules/
