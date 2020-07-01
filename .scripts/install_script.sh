#!/usr/bin/env bash

install() {
    cleanFTPUserList
    clean_logs

    docker-compose -f ${BASIC_CONFIG} -f ${CONFIG} up --build -d

    # Start needed services
    initial || error_exit "Initial script error"

    # Install dependencies
    confirm "Do you want to install composer dependencies?  [y/N]"
    if [[ $? == 1 ]]
    then
        executeInContainer "php composer.phar install"
    fi

    # Apply migrations
    confirm "Do you want to refresh migrations?  [y/N]"
    if [[ $? == 1 ]]
    then
        executeInContainer "php artisan migrate:fresh"
    fi

    # Apply seeds
    confirm "Do you want to apply seeds?  [y/N]"
    if [[ $? == 1 ]]
    then
        executeInContainer "php artisan db:seed"
    fi

     # Generate public storage link
    confirm "Do you want to generate public storage symlink?  [y/N]"
    if [[ $? == 1 ]]
    then
        executeInContainer "php artisan storage:link"
    fi

    # Set up domain
    set_domains

    echo -e "${GREEN}${APP_NAME} installed!${NC}"
}
