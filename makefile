#!/usr/bin/make

.SILENT: clean
.PHONY: all
.DEFAULT_GOAL := help

PHP = docker run --rm -v $(PWD):/var/www/html -w /var/www/html php:8.4-fpm php
APP = docker-compose exec app
APP_COMPOSER = docker-compose exec app composer
APP_PHP = docker-compose exec app php
APP_NPM = docker-compose exec app npm

##@ Development resources

setup: ## Setup the project
	@make check-docker
	@cp .env.example .env
	mkdir -p ./storage/framework/cache
	mkdir -p ./storage/framework/sessions
	mkdir -p ./storage/framework/views
	mkdir -p ./storage/app/public
	mkdir -p ./bootstrap/cache
	docker-compose up -d --build --force-recreate
	$(APP_COMPOSER) install --no-interaction --no-plugins --no-scripts
	$(APP_NPM) install && $(APP_NPM) run build
	$(APP_PHP) artisan key:generate
	$(APP) touch /var/www/html/database/database.sqlite
	$(APP_PHP) artisan migrate --force

container: ## Access the application container
	docker-compose exec -it  app bash

ci: ## Run continuous integration tests
	make composer ci

check-docker: ## Check if Docker is installed
	@docker --version > /dev/null 2>&1 || (echo "Docker is not installed. Please install Docker and try again." && exit 1)

help: ## Show this help message
	@echo "Usage: make [command]"
	@echo ""
	@echo "Commands available:"
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "  %-15s %s\n", $$1, $$2}' $(MAKEFILE_LIST) | sort
