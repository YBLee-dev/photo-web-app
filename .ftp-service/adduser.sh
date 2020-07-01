#!/usr/bin/env bash

# Log filename
LOG_FILE='/srv/app/storage/logs/ftp_user_management.log'

# Read options
while getopts u:p: option
do
case "${option}"
    in
    u)  USERNAME=${OPTARG};;
    p)  PASSWORD=${OPTARG};;
    esac
done

# Write to log
write_to_log()
{
    echo -e "`date '+%Y-%m-%d %H:%M:%S'`: $1" >>${LOG_FILE}
}

# Common function for the error handling
log_error_and_exit()
{
    write_to_log $1
    exit 1
}

# Processing start log
write_to_log "${USERNAME} creating started"

# Add user
adduser --disabled-password --quiet --gecos "" ${USERNAME} &>>${LOG_FILE} || log_error_and_exit 'user adding error'

echo "${USERNAME}:${PASSWORD}" | chpasswd  &>>${LOG_FILE} || log_error_and_exit 'password setting error'

# Add user FTP directory
USER_FPT_DIR="/home/${USERNAME}/ftp"
mkdir ${USER_FPT_DIR} &>>${LOG_FILE} || log_error_and_exit 'ftp directory creating error'
chown nobody:nogroup ${USER_FPT_DIR}
chmod a-w ${USER_FPT_DIR}

# Add user uploads directory
USER_FILES_DIR="$USER_FPT_DIR/uploads"
mkdir ${USER_FILES_DIR} &>>${LOG_FILE} || log_error_and_exit 'user adding error'
chown "${USERNAME}":"${USERNAME}" ${USER_FILES_DIR}

# Prepare file link for user uploads directory
ln -s ${USER_FILES_DIR} "/srv/app/storage/app/ftp/${USERNAME}" &>>${LOG_FILE} || log_error_and_exit 'symbol link creating error'

# Add FTP user
echo "${USERNAME}" >> /etc/vsftpd.userlist || log_error_and_exit 'adding FTP user error'

write_to_log "${USERNAME} was CREATED"

echo 'Done!'
