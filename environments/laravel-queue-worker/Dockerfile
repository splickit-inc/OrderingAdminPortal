FROM ubuntu:16.04
ENV DEBIAN_FRONTEND noninteractive
MAINTAINER Oscar Jara <oajara@gmail.com>

WORKDIR /var/www/portal

RUN apt-get update && \
    apt-get -y install unzip && \
    apt-get -y install vim && \   
    apt-get -y install curl && \ 
    apt-get -y install php7.0 php7.0-xml php7.0-curl php7.0-json php7.0-mysql php7.0-mbstring php7.0-mcrypt php7.0-soap php7.0-gd php-xdebug && \    
    apt-get -y install supervisor && \
    apt-get -y install cron && \
    rm -rf /var/lib/apt/lists/* 

# Use local configuration
COPY supervisord.conf /etc/supervisor/supervisord.conf

# Set scheduling: https://laravel.com/docs/5.5/scheduling#introduction
RUN echo "* * * * * php /var/www/portal/artisan schedule:run >> /dev/null 2>&1" | crontab -

# Run supervisor
CMD ["supervisord", "-c", "/etc/supervisor/supervisord.conf"]
