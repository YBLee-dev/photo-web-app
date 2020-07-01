FROM leadworker/laravel:full

#####################
# Project
#####################

# Install FTP
RUN apt-get update
RUN apt-get install -y vsftpd

RUN docker-php-ext-install exif

## Install angular cli
RUN npm install -g @angular/cli@7.3.9
