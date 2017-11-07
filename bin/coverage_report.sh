#!/usr/bin/env bash

docker-compose run --rm devtools /bin/bash -c "phpcov merge --html=var/coverage/html var/coverage"
echo "Coverage report generated in var/coverage/html"
