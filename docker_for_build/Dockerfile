FROM ubuntu:16.04

LABEL Description="MEICAN Image" \
      Author="Leonardo Lauryel Batista dos Santos"

ARG MEICAN_VERSION \ 
   DEBIAN_FRONTEND=noninteractive \
   DEBCONF_NONINTERACTIVE_SEEN=true

ENV MYSQL_DATABASE=$MYSQL_DATABASE \
   MYSQL_USER=$MYSQL_USER \
   MYSQL_PASSWORD=$MYSQL_PASSWORD \
   MEICAN_DIR=/home/meican/meican-$MEICAN_VERSION
   
RUN apt-get update \
 && apt-get install -yq --no-install-recommends \
    apt-utils \
    curl \
    sudo \
    zip \
    unzip \
    # Install apache
    apache2 \
    # Install php 7.0
    php7.0 \
    php7.0-mysql \
    php7.0-mbstring \
    php7.0-curl \
    php7.0-soap \
    php7.0-xml \
    libapache2-mod-php \
 && rm -rf /var/lib/apt/lists/*

RUN adduser --disabled-password --gecos '' meican \
 && usermod -aG sudo meican \
 && su meican -c "curl -kL https://github.com/ufrgs-hyman/meican/archive/$MEICAN_VERSION.tar.gz | tar xzC /home/meican"

WORKDIR $MEICAN_DIR

RUN su meican -c "curl -kO https://getcomposer.org/download/1.9.3/composer.phar" \
 && su meican -c "php composer.phar global require "fxp/composer-asset-plugin:~1.4.4"" \
 && ln -s $MEICAN_DIR/web /var/www/meican \
 && a2enmod rewrite

COPY 000-default.conf /etc/apache2/sites-available/
COPY wait-for-mysql.sh $MEICAN_DIR/docker/
COPY docker-entrypoint.sh $MEICAN_DIR/docker/

EXPOSE 80

ENTRYPOINT $MEICAN_DIR/docker/wait-for-mysql.sh 'db' "$MEICAN_DIR/docker/docker-entrypoint.sh"
