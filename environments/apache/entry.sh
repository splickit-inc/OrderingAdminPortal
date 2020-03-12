#!/bin/bash -e

if [ "$ENABLE_NEWRELIC" != "true" ]
then
        echo "Disabling NewRelic..."
	phpdismod newrelic || true
	rm -f /etc/php/7.0/apache2/conf.d/20-newrelic.ini || true
	rm -f /etc/php/7.0/cli/conf.d/20-newrelic.ini || true
fi

if [ "$ENVIRONMENT" == "development" ]
then
	# Enable XDEBUG extension
	echo "Enabling xdebug..."
	phpenmod xdebug

	# Fix for permission issues. UID 1000 is the uid for user vagrant in the dev environment.
	# This way www-data user inside the container will have write access to the files and dirs.
	echo "Chaning UID/GID for user www-data to 1000"
	sed -i 's/www-data:x:33:33:www-data/www-data:x:1000:1000:www-data/g' /etc/passwd
else
        echo "Disabling Xdebug..."
        phpdismod xdebug
fi


# Create logs dir. TODO: parametrice dir
mkdir -p /var/www/portal/logs && chmod +rw /var/www/portal/logs
mkdir -p /var/www/portal/storage/logs && chmod +rw /var/www/portal/storage/logs

# Install Dependencies
#cd public
#
#if [ -e "./package.json" ]
#then
#	npm install
#fi
#
#if [ -e "./bower.json" ]
#then
#	bower install --allow-root --config.interactive=false
#	grunt wiredep
#	grunt build
#fi
#
#cd -

# Start service
exec "apache2-foreground"
