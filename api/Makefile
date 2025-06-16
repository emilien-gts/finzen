# This Makefile manages Symfony API tasks: install, server control, DB operations, fixtures, and cache

.PHONY: help install start stop cache-clear cache-warm db-update db-drop db-create db-reset fixtures

help: ## Show available commands and their descriptions
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-15s\033[0m %s\n", $$1, $$2}'


install: ## Install PHP dependencies using Composer
	composer install
	php bin/console doctrine:database:drop --force --if-exists
	php bin/console doctrine:database:create --if-not-exists
	php bin/console doctrine:schema:update --force

start: ## Start the Symfony local web server in daemon mode
	symfony server:start -d

stop: ## Stop the Symfony local web server
	symfony server:stop

cc: ## Clear the Symfony application cache
	php bin/console cache:clear

db-update: ## Update the database schema to match current entities (no migrations)
	php bin/console doctrine:schema:update --force

db-create: ## Create the database if it does not exist
	php bin/console doctrine:database:create --if-not-exists

db-drop: ## Drop the database if it exists
	php bin/console doctrine:database:drop --force --if-exists

db-reset: ## Reset the database: drop, create, update schema, and load fixtures
	php bin/console doctrine:database:drop --force --if-exists
	php bin/console doctrine:database:create --if-not-exists
	php bin/console doctrine:schema:update --force
	php bin/console doctrine:fixtures:load --no-interaction

seed: ## Load data fixtures into the database
	php bin/console doctrine:fixtures:load --no-interaction

test: ## Run tests
	php bin/phpunit --testdox

check: ## Run static analysis and code style checks (PHP-CS-Fixer + PHPStan)
	vendor/bin/php-cs-fixer fix
	vendor/bin/phpstan analyse
