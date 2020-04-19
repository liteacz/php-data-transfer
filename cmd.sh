#!/bin/bash

# ================== #
# ===  COMMANDS  === #
# ================== #

case "$1" in
    "attach-to-shell")
        USER="$2"
        SERVICE="$3"

        [[ -z ${SERVICE} ]] && SERVICE="php-cli"
        [[ -z ${USER} ]] && USER=$UID

        docker-compose exec -u $USER ${SERVICE} sh
    ;;
esac