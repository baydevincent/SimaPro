.PHONY: help setup up down restart logs shell db-shell migrate fresh clean pull

help: ## Show this help message
	@echo "SimaPro Docker Commands"
	@echo ""
	@echo "Usage: make [command]"
	@echo ""
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-15s\033[0m %s\n", $$1, $$2}'

setup: ## Full setup: clone repo, build containers, install dependencies
	@chmod +x setup-docker.sh
	@./setup-docker.sh

up: ## Start all containers
	docker-compose up -d

down: ## Stop all containers
	docker-compose down

restart: ## Restart all containers
	docker-compose restart

logs: ## View logs
	docker-compose logs -f

logs-app: ## View app logs
	docker-compose logs -f app

logs-nginx: ## View nginx logs
	docker-compose logs -f nginx

logs-db: ## View database logs
	docker-compose logs -f postgresql

shell: ## Access app container
	docker-compose exec app bash

db-shell: ## Access PostgreSQL
	docker-compose exec postgresql psql -U simapro -d simapro

migrate: ## Run migrations
	docker-compose exec app php artisan migrate

fresh: ## Fresh migration with seeding
	docker-compose exec app php artisan migrate:fresh --seed

install: ## Install PHP dependencies
	docker-compose exec app composer install

npm-install: ## Install NPM dependencies
	docker-compose exec node npm install

build-assets: ## Build assets for production
	docker-compose exec node npm run build

dev: ## Build assets for development
	docker-compose exec node npm run dev

watch: ## Watch assets for changes
	docker-compose exec node npm run watch

cache-clear: ## Clear all cache
	docker-compose exec app php artisan optimize:clear

key-generate: ## Generate application key
	docker-compose exec app php artisan key:generate

storage-link: ## Create storage symlink
	docker-compose exec app php artisan storage:link

pull: ## Pull latest changes from GitHub
	git pull origin main || git pull origin master
	docker-compose restart app

backup-db: ## Export database to backup.sql
	docker-compose exec postgresql pg_dump -U simapro simapro > backup.sql

restore-db: ## Import database from backup.sql
	docker-compose exec -T postgresql psql -U simapro -d simapro < backup.sql

clean: ## Remove all containers and volumes
	@echo "WARNING: This will remove all containers and volumes!"
	@echo "Are you sure? (y/n)"
	@read ans && [ $${ans:-N} = y ]
	docker-compose down -v

rebuild: ## Rebuild containers without cache
	docker-compose build --no-cache
	docker-compose up -d
