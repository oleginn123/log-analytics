include .docker/.env

export OS_TYPE = $(shell uname)
export GID = $(shell id -g)
export GID = $(shell id -g)

build:
	docker compose -f .docker/docker-compose.yml build
rebuild:
	docker compose -f .docker/docker-compose.yml build --no-cache
up:
	docker compose -f .docker/docker-compose.yml up -d --remove-orphans
down:
	docker compose -f .docker/docker-compose.yml down -v
logs:
	docker compose -f .docker/docker-compose.ymllogs -f
composer-install:
	docker compose -f .docker/docker-compose.yml exec -T -u www-data app composer install
install:
	make up
	docker compose -f .docker/docker-compose.yml exec -T -u www-data app composer install --no-scripts --prefer-dist
	@if [ $(APP_ENV) = "production" ]; then\
    	docker compose -f .docker/docker-compose.yml exec -T -u www-data app bin/console system:setup;\
    else \
    	cp .env.local.dist .env;\
    fi
ssh:
	docker compose -f .docker/docker-compose.yml exec -u www-data app sh
