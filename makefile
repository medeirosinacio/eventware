#!/usr/bin/make

.SILENT: clean
.PHONY: all
.DEFAULT_GOAL := help
PHP_VERSION := 83
PHP_CONTAINER := opencodeco/phpctl:php$(PHP_VERSION)-devcontainer

##@ Development resources

setup: ## Setup the project
	@make check-docker
	docker stop $(docker ps -aq) || true
	make install-dependencies
	make playground

playground: ## Start a PHP playground dockerized environment
	@make check-docker
	@docker run --rm -it -v ./:/app -w /app $(PHP_CONTAINER) bash

development: ## Start a PHP playground dockerized environment
	@make check-docker
	@docker run --rm -it -v ./:/app -w /app $(PHP_CONTAINER) php bin/console.php dev:eventware

install-dependencies: ## Install dependencies
	@make check-docker
	@docker run --rm -it -v ./:/app -w /app $(PHP_CONTAINER) composer install

ci: ## Run the CI pipeline
	@docker run --rm -it -v ./:/app -w /app $(PHP_CONTAINER) composer ci

check-docker: ## Check if Docker is installed
	@docker --version > /dev/null 2>&1 || (echo "Docker is not installed. Please install Docker and try again." && exit 1)

help: ## Show this help message
	@echo "Usage: make [command]"
	@echo ""
	@echo "Commands available:"
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "  %-15s %s\n", $$1, $$2}' $(MAKEFILE_LIST) | sort
