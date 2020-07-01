#!/usr/bin/env bash

templates_path=".service-main/.apache/hosts-templates/"
result_path=".service-main/.apache/sites-available/"
default_conf_name="000-default.conf"

files_src_path="/srv/app/.service-main/.apache/sites-available/"
files_dest_path="/etc/apache2/sites-available/"

#########################################################
# Generate selfsigned certificate for domain            #
#########################################################
generate_selfsigned_cert(){
   domain=$1 || error_exit "Domain didn't specified"
   destination="/srv/ssl/${domain}"
   check_path=".service-main/.ssl/${domain}"

   executeInContainer "mkdir $destination"

   # Generate certificates
   dhparam_path=/srv/ssl/dhparam.pem
   dhparam_check=.service-main/.ssl/dhparam.pem
   if [[ ! -f ${dhparam_check} ]]; then
       executeInContainer "openssl dhparam -out ${dhparam_path} 2048"
   else
       echo "$dhparam_path exists"
   fi

   privkey_path=${destination}/privkey.key
   fullchain_path=${destination}/fullchain.cert
   privkey_check=${check_path}/privkey.key
   if [[ ! -f ${privkey_check} ]]; then
        executeInContainer "openssl req \
                -new \
                -newkey rsa:4096 \
                -days 3650 \
                -nodes \
                -x509 \
                -subj "/C=CA/ST=OT/L=OT/O=EMC/CN=$domain" \
                -keyout ${privkey_path} \
                -out ${fullchain_path}"
    else
        echo "$privkey_path exists"
   fi
}

#########################################################
# Install localhost domain for simple local development #
#########################################################

add_localhost_domain(){
    # Prepare default config
    cp "${templates_path}000-default-localhost.conf.template" "${result_path}${default_conf_name}"

    # Copy prepared config into the container
    executeInContainer "cp ${files_src_path}${default_conf_name} ${files_dest_path}${default_conf_name}"

    # Turn on default config
    executeInContainer "a2ensite 000-default.conf"

    executeInContainer "service apache2 reload"
}

#################################################################
# Install http and https domains for development and production #
#################################################################
add_domains_with_ssl(){
    domain=$1

    # Prepare selfsigned certificates
    generate_selfsigned_cert $domain

    # Copy and enable apache ssl config
    executeInContainer "cp /srv/app/.service-main/.apache/default-ssl.conf /etc/apache2/conf-available/ssl-params.conf"
    executeInContainer "a2enconf ssl-params"

    # Prepare paths
    http_conf="${domain}.conf"
    https_conf="${domain}-ssl.conf"

    http_conf_path="${result_path}${http_conf}"
    https_conf_path="${result_path}${https_conf}"

    # Copy templates
    cp "${templates_path}http.conf.template" ${http_conf_path}
    cp "${templates_path}https.conf.template" ${https_conf_path}

    # Update domain names
    sed -i "s/{{domain_name}}/$domain/" ${http_conf_path}
    sed -i "s/{{domain_name}}/$domain/" ${https_conf_path}

    # Install domain names
    # Copy configs
    executeInContainer "cp ${files_src_path}${http_conf} ${files_dest_path}${http_conf}"
    executeInContainer "cp ${files_src_path}${https_conf} ${files_dest_path}${https_conf}"

    # Turn on HTTP and HTTPS configs
    executeInContainer "a2ensite ${http_conf}"
    executeInContainer "a2ensite ${https_conf}"

    # Turn off default config
    executeInContainer "a2dissite 000-default.conf"

    executeInContainer "service apache2 reload"
}

#################################################################
# LetsEncrypt certificates generation                           #
#################################################################
letsencrypt_initializing(){
    confirm "Do you want to install LetsEncrypt certificates?  [y/N]"
    if [[ $? == 1 ]]
    then
        executeInContainer "certbot --apache"
    fi
}

#################################################################
# Install domains                                               #
#################################################################
add_domain() {
    if [[ "${1}" == "" ]]
    then
       confirm "You didn't specified domain name. Do you want to activate localhost?  [y/N]"
        if [[ $? == 1 ]]
        then
            add_localhost_domain
        fi
    else
        add_domains_with_ssl ${1}
        letsencrypt_initializing
    fi
}

set_domains(){
     confirm "Do you want to install domains?  [y/N]"
    if [[ $? == 1 ]]
    then
        domain=$(echo ${APP_URL} | sed "s/https\?:\/\///")
        add_domain ${domain}
    fi
}
