#!make
include .env
export $(shell sed 's/=.*//' .env)

APP_CONTAINER := "app"

.SILENT:
.PHONY:

build:
	docker-compose -f docker/docker-compose.yml build

up:
	docker-compose -f docker/docker-compose.yml up -d

stop:
	docker-compose -f docker/docker-compose.yml stop

restart:
	@make stop
	@make up

enter:
	-docker-compose -f docker/docker-compose.yml exec --user=$(DOCKER_USER) $(APP_CONTAINER) bash

install:
	docker-compose -f docker/docker-compose.yml exec--user=$(DOCKER_USER) $(APP_CONTAINER) composer install
