#!/usr/bin/env bash

## Validate application name
validate_app_name(){
    if [[ "${APP_NAME}" == "Laravel" ]]; then
        confirm "It looks like you used default application name APP_NAME=${APP_NAME}.
Are you sure want to use this app name for container name generation? [y/N]"
        if [[ $? == 0 ]]
        then
            echo -e "${YELLOW}Please, update APP_NAME variable in .env file to set new application name${NC}"
            exit 1
        fi
    fi
}

# Preparing .env file
env_file_preparation(){
    if [[ ! -f ".env.example" ]]; then
        error_exit ".env.example was not found. Please, prepare .env file by yourself"
    fi

    confirm "Do you want to prepare new .env based on .env.example?  [y/N]"
    if [[ $? == 0 ]]
    then
        error_exit "Please, prepare .env file by yourself"
    fi

    executeInContainer "cp .env.example .env"
}

validate_queue_config() {
     if [[ "${QUEUE_DRIVER}" != "database" ]]; then
        error_exit "Please, update you QUEUE_DRIVER to 'database' in your .env"
    fi
}

# Prepare and load .env file
env_load(){
    if [[ ! -f ".env" ]]; then
        confirm "It looks like you has no .env file yet. Do you want to prepare new .env file now?  [y/N]"
        if [[ $? == 1 ]]
        then
            env_file_preparation || error_exit "Please, prepare .env file by yourself"
        else
            echo -e "${RED}You should prepare .env file first${NC}"
            exit 0;
        fi
    fi

    # Load .env file
    export $(grep -v '^#' .env | xargs -d '\n')

    # Validate if application name set not as default
    validate_app_name

    # Validate queue setting
    validate_queue_config
}
