DOCKER_COMPOSE = docker-compose
EXEC_PHP = $(DOCKER_COMPOSE) exec php
EXEC_MYSQL = $(DOCKER_COMPOSE) exec mysql
COMPOSER = $(EXEC_PHP) composer
NPM = $(EXEC_PHP) npm
CONSOLE = $(EXEC_PHP) php bin/console


build: ## Build les containers Docker
	$(DOCKER_COMPOSE) build

up: ## Monte les containers Docker
	$(DOCKER_COMPOSE) up -d --remove-orphans

down: ## Démonte les containers Docker
	$(DOCKER_COMPOSE) down

stop: ## stop all containers
	$(DOCKER_COMPOSE) kill
	$(DOCKER_COMPOSE) rm -v --force

init: build up vendor node_modules npm-build db migrate fixtures db-test migrate-test fixtures-test

bash: ## Permet d'accéder au bash du container PHP
	$(EXEC_PHP) bash

mysql: ## Permet d'accéder directement à la console du container MySQL
	$(EXEC_MYSQL) mysql -u root -proot

db: vendor-no-scripts ## Drop et créé la base de données
	$(CONSOLE) doctrine:database:drop --force --if-exists
	$(CONSOLE) doctrine:database:create --if-not-exists

db-test: vendor-no-scripts ## Drop et créé la base de données
	$(CONSOLE) doctrine:database:drop --force --if-exists --env=test
	$(CONSOLE) doctrine:database:create --if-not-exists --env=test

migrate: vendor-no-scripts ## Lance les migrations en attente
	$(CONSOLE) doctrine:migrations:migrate --no-interaction --allow-no-migration


migrate-test: vendor-no-scripts ## Lance les migrations en attente
	$(CONSOLE) doctrine:migrations:migrate --no-interaction --allow-no-migration --env=test

fixtures:
	$(CONSOLE) doctrine:fixtures:load --no-interaction

fixtures-test:
	$(CONSOLE) doctrine:fixtures:load --no-interaction --env=test

lt: vendor
	$(CONSOLE) lint:twig templates

ly: vendor
	$(CONSOLE) lint:yaml config --parse-tags

cc: ## Vide le cache
	$(CONSOLE) cache:clear --no-warmup

vendor: composer.lock
	$(COMPOSER) install -n

vendor-no-scripts: composer.lock
	$(COMPOSER) install -n --no-scripts

node_modules:
	$(NPM) install

npm-build:
	$(NPM) run build

watch:
	$(NPM) run watch

single-test:
	@if		[-z "$(TEST)" ]; then \
            echo "Veuillez spécifier un nom de test en utilisant la variable TEST, par exemple: TEST=ProductTest make single-test" ; \
        else \
            $(EXEC_PHP) vendor/phpunit/phpunit/phpunit --filter="$(TEST)"; \
        fi
