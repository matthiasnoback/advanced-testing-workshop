#!/usr/bin/env bash

DOCKER_COMPOSE="docker-compose -f docker-compose.yml -f docker-compose.test.yml"
${DOCKER_COMPOSE} up -d web
${DOCKER_COMPOSE} run --rm devtools /bin/bash -c "vendor/bin/phpunit && vendor/bin/behat -vvv"
${DOCKER_COMPOSE} stop web
