#!/usr/bin/env bash

# todo add checking for vsftpd.userlist
# todo add DB users and system users sync

# Docker compose config files
BASIC_CONFIG=".docker/configs/docker-compose.yml"
LOCAL_CONFIG=".docker/configs/docker-compose.local.yml"
DEV_CONFIG=".docker/configs/docker-compose.dev.yml"
STAGE_CONFIG=".docker/configs/docker-compose.stage.yml"
PROD_CONFIG=".docker/configs/docker-compose.prod.yml"

# Load .env file
env_load

# Cleanup FTP users list
cleanFTPUserList() {
    > ./.ftp-service/vsftpd.userlist
}

# Read input
for i in "$@"
do
case $i in
    install|up|start|restart|stop|down|sync|update|in|domains|ssl|clear-logs|front-rebuild)
    ACTION="$i"
    shift # past argument=value
    ;;
    -a=*|--action=*)
    ACTION="${i#*=}"
    shift # past argument=value
    ;;
    local|dev|stage|prod)
    MODE="$i"
    shift # past argument=value
    ;;
    -m=*|--mode=*)
    MODE="${i#*=}"
    shift # past argument=value
    ;;
    *)
          # unknown option
    ;;
esac
done

# Apply config
if [[ ${MODE} == "local" ]]
then
    CONFIG=${LOCAL_CONFIG}
elif [[ ${MODE} == "dev" ]]
then
    CONFIG=${DEV_CONFIG}
elif [[ ${MODE} == "stage" ]]
then
    CONFIG=${STAGE_CONFIG}
elif [[ ${MODE} == "prod" ]]
then
    CONFIG=${PROD_CONFIG}
else
    echo -e "${YELLOW}Environment was not defined${NC}"
    confirm "Do you want to use 'local' environment? [Y/n]"
    if [[ $? == 1 ]]; then
        CONFIG=${LOCAL_CONFIG}
     else
        error_exit "Please, define environment first"
     fi
fi

# First install script
if [[ ${ACTION} == "install" ]]
then
    install
fi

# Update script
if [[ ${ACTION} == "update" ]]
then
    docker-compose -f ${BASIC_CONFIG} -f ${CONFIG} up -d
    initial
fi

# Up script
if [[ ${ACTION} == "up" ]]
then
    docker-compose -f ${BASIC_CONFIG} -f ${CONFIG} up -d
    initial
fi

# Run installed script
if [[ ${ACTION} == "start" ]]
then
    docker-compose -f ${BASIC_CONFIG} -f ${CONFIG} start
    initial
fi

# Run installed script
if [[ ${ACTION} == "restart" ]]
then
    docker-compose -f ${BASIC_CONFIG} -f ${CONFIG} stop
    docker-compose -f ${BASIC_CONFIG} -f ${CONFIG} start
    initial
fi

# Stop script
if [[ ${ACTION} == "stop" ]]
then
    docker-compose -f ${BASIC_CONFIG} -f ${CONFIG} stop
fi

# Down script
if [[ ${ACTION} == "down" ]]
then
    docker-compose -f ${BASIC_CONFIG} -f ${CONFIG} down
    cleanFTPUserList
fi

# Assets sync script
if [[ ${ACTION} == "sync" ]]
then
    sync
fi

# Assets sync script
if [[ ${ACTION} == "in" ]]
then
    docker exec -it $(getContainerName) /bin/bash
fi

# Assets sync script
if [[ ${ACTION} == "domains" ]]
then
    set_domains
fi

# Clear app logs
if [[ ${ACTION} == "clear-logs" ]]
then
    rm -rf storage/logs/*
fi

## Clear app logs
#if [[ ${ACTION} == "front-rebuild" ]]
#then
#    executeInContainer "cd resources/build-app && ng build"
##    executeInContainer "resources/assets/_gulp-builder/gulp build"
#fi

