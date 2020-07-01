#!/usr/bin/env bash

initial() {
    # Set directories permissions for Laravel correct work
    executeInContainer "chmod 777 -R storage/ bootstrap/cache"

    # Set root as owner for VSFTPD correct work
    executeInContainer "chown root:root /etc/vsftpd.conf /etc/vsftpd.userlist"

    executeInContainer "service vsftpd start"
    executeInContainer "service supervisor start"
    executeInContainer "service cron start"
}
