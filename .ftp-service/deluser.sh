#!/usr/bin/env bash

# Log filename
LOG_FILE='/srv/app/storage/logs/ftp_user_management.log'

while getopts u: option
do
case "${option}"
    in
    u)  USERNAME=${OPTARG};;
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

# Manual cleanup if user not found
manual_cleanup()
{
    write_to_log "User ${USERNAME} not found. Manual cleanup started"

    rm -rf /home/${USERNAME}

    write_to_log "${USERNAME} was manually DELETED"
}

# Starting log
write_to_log "${USERNAME} deleting started"

# Remove user with all files
userdel -r -f "${USERNAME}"  || manual_cleanup

# Remove FTP user
cp -f /etc/vsftpd.userlist /etc/vsftpd.userlist_new
sed -i "/${USERNAME}/d" /etc/vsftpd.userlist_new
echo "$(cat /etc/vsftpd.userlist_new)" > /etc/vsftpd.userlist
rm /etc/vsftpd.userlist_new

# Remove symbol link
rm "/srv/app/storage/app/ftp/${USERNAME}" || log_error_and_exit 'symlink deleting error'

# Finishing log
write_to_log "${USERNAME} was DELETED"
