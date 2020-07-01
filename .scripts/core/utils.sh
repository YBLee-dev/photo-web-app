#!/usr/bin/env bash

# Prepare container name based on .env APP_NAME
getContainerName(){
    echo "${APP_NAME}_main-service"
}

# Execute commands inside the main container
executeInContainer() {
    container=$(getContainerName)
    docker exec -it ${container} $1
}

# Cleanup logs
clean_logs() {
    rm -rf storage/logs/*
    echo -e "${BLUE}Logs were cleaned${NC}"
}

# Colors
RED='\033[1;31m'
GREEN='\033[0;32m'
YELLOW='\033[0;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Common confirmation function
confirm() {
# call with a prompt string or use a default
read -r -p "${1:-Are you sure? [y/N]} " response
case "$response" in
    [yY][eE][sS]|[yY])
        return 1
        ;;
    *)
        return 0
        ;;
esac
}

# Common function for the error handling
error_exit()
{
	echo -e "${RED}$1${NC}" 1>&2
	exit 1
}

