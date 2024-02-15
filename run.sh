#!/bin/sh
PATH=/usr/sbin:/sbin:/usr/bin:/bin
export PATH="$PATH:"/usr/local/bin/

docker compose -f ${PWD}/containers/php/docker-compose.yaml stop
docker compose -f ${PWD}/containers/php/docker-compose.yaml build --pull --no-cache
docker compose -f ${PWD}/containers/php/docker-compose.yaml up -d

docker compose -f ${PWD}/containers/postgres/docker-compose.yaml stop
docker compose -f ${PWD}/containers/postgres/docker-compose.yaml build --pull --no-cache
docker compose -f ${PWD}/containers/postgres/docker-compose.yaml up -d

docker compose -f ${PWD}/containers/nginx/docker-compose.yaml build stop
docker compose -f ${PWD}/containers/nginx/docker-compose.yaml build --pull --no-cache
docker compose -f ${PWD}/containers/nginx/docker-compose.yaml up -d
