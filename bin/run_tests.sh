#!/usr/bin/env bash

set -eu

DOCKER_COMPOSE="docker-compose -f docker-compose.yml -f docker-compose.test.yml"
${DOCKER_COMPOSE} run --rm devtools /bin/bash -c \
    "vendor/bin/phpunit --testsuite unit -v \
     && vendor/bin/phpunit --testsuite integration -v \
     && vendor/bin/behat --suite acceptance -vvv"
${DOCKER_COMPOSE} up -d web
${DOCKER_COMPOSE} run --rm devtools /bin/bash -c "vendor/bin/behat --suite end_to_end -vvv"
${DOCKER_COMPOSE} stop web
