### CHANGELOG:
### 1.0: Disabled grunt assets compilation in container entry point. Container expects to have compiled assets in the repo.

FROM ubuntu:16.04
ENV DEBIAN_FRONTEND noninteractive
MAINTAINER Oscar Jara <oajara@gmail.com>

WORKDIR /var/www/portal

RUN apt-get update && \
    apt-get -y install git && \
    apt-get -y install unzip && \
    apt-get -y install vim && \   
    apt-get -y install curl && \ 
    apt-get -y install apache2 libapache2-mod-php7.0 ssl-cert --no-install-recommends && \
    apt-get -y install mysql-client && \    
    apt-get -y install php7.0 php7.0-xml php7.0-curl php7.0-json php7.0-mysql php7.0-mbstring php7.0-mcrypt php7.0-soap php7.0-gd php-xdebug && \    
    apt-get -y install nodejs-legacy && \    
    apt-get -y install npm && \    
    rm -rf /var/lib/apt/lists/* && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# NPM dependencies
RUN npm install -g generator-cg-angular bower grunt grunt-wiredep

# Install New Relic daemon
RUN apt-get update && \
    apt-get -yq install wget && \
    wget -O - https://download.newrelic.com/548C16BF.gpg | apt-key add - && \
    echo "deb http://apt.newrelic.com/debian/ newrelic non-free" > /etc/apt/sources.list.d/newrelic.list
 
 
# Setup environment variables for initializing New Relic
ENV NR_INSTALL_SILENT 1
ENV NR_INSTALL_KEY 8c5c266b5909955933574169093a97a1cac8878d
ENV NR_APP_NAME 'Admin Portal'

RUN apt-get update && \
    apt-get -yq install newrelic-php5

#RUN newrelic-install install
RUN sed -i "s/newrelic.appname = \"PHP Application\"/newrelic.appname = \"${NR_APP_NAME}\"/" /etc/php/7.0/mods-available/newrelic.ini
RUN sed -i "s/newrelic.license = \"\"/newrelic.license = \"${NR_INSTALL_KEY}\"/" /etc/php/7.0/mods-available/newrelic.ini

    
ENV APACHE_CONFDIR /etc/apache2
ENV APACHE_ENVVARS $APACHE_CONFDIR/envvars

# PHP files should be handled by PHP, and should be preferred over any other file type
RUN { \
		echo '<FilesMatch \.php$>'; \
		echo '\tSetHandler application/x-httpd-php'; \
		echo '</FilesMatch>'; \
		echo; \
		echo 'DirectoryIndex disabled'; \
		echo 'DirectoryIndex index.php index.html'; \
		echo; \
		echo '<Directory /var/www/>'; \
		echo '\tOptions -Indexes'; \
		echo '\tAllowOverride All'; \
		echo '</Directory>'; \
	} | tee "$APACHE_CONFDIR/conf-available/docker-php.conf" \
	&& a2enconf docker-php

# Copy the conf file
COPY environments/apache/portal_vhost.conf /etc/apache2/sites-available/portal.conf

# logs should go to stdout / stderr
RUN mkdir logs && set -ex \
        && . "$APACHE_ENVVARS" \
    && ln -sfT /dev/stderr /var/www/portal/logs/portal-error.log \
        && ln -sfT /dev/stdout /var/www/portal/logs/portal-access.log \
        && ln -sfT /dev/stdout /var/log/apache2/other_vhosts_access.log \
    && ln -sfT /dev/stderr /var/log/apache2/error.log \
        && ln -sfT /dev/stdout /var/log/apache2/access.log 

# We use our own version for xdebug.ini
COPY environments/apache/xdebug.ini /etc/php/7.0/mods-available/xdebug.ini

# Other custom PHP INI settings
COPY environments/apache/custom_php.ini /etc/php/7.0/apache2/conf.d/21-portal-custom.ini

RUN a2dissite 000-default && a2ensite portal && a2enmod rewrite

# Cronjobs
#COPY environments/apache/cronjob

COPY environments/apache/apache2-foreground /usr/local/bin/
COPY environments/apache/entry.sh /usr/local/bin/

EXPOSE 8080

CMD ["/usr/local/bin/entry.sh"]

