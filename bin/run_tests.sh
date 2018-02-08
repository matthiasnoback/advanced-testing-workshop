#!/usr/bin/env bash

set -eu

DOCKER_COMPOSE="docker-compose -f docker-compose.yml -f docker-compose.test.yml"
${DOCKER_COMPOSE} run --rm devtools /bin/bash -c \
    "vendor/bin/phpunit --testsuite unit --coverage-php var/coverage/unit.cov -v \
     && vendor/bin/phpunit --testsuite integration --coverage-php var/coverage/integration.cov -v \
     && vendor/bin/behat --suite acceptance -vvv"
${DOCKER_COMPOSE} up -d web
${DOCKER_COMPOSE} run --rm devtools /bin/bash -c "vendor/bin/behat --suite system -vvv"
${DOCKER_COMPOSE} stop web

docker-compose run --rm devtools /bin/bash -c "phpcov merge --html=var/coverage/html var/coverage"
echo "Coverage report generated in var/coverage/html"
