#!/usr/bin/env bash

docker-compose up -d web
docker-compose run --rm devtools /bin/bash -c "vendor/bin/phpunit && vendor/bin/behat -vvv"
