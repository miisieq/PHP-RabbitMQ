#!make
include .env
export $(shell sed 's/=.*//' .env)

APP_CONTAINER := "app"

.SILENT:
.PHONY:

build:
	docker-compose build

up:
	docker-compose up -d

stop:
	docker-compose stop

enter:
	-docker-compose exec $(APP_CONTAINER) bash

install:
	docker-compose exec $(APP_CONTAINER) composer install
