FROM ubuntu:16.04

MAINTAINER Leonardo Lauryel Batista dos Santos

LABEL Description="MEICAN Image"

ARG MYSQL_ROOT_PASSWORD
ARG MYSQL_DATABASE
ARG MYSQL_USER
ARG MYSQL_PASSWORD
ARG MEICAN_VERSION
ARG MEICAN_DIR=/home/meican/meican-$MEICAN_VERSION

## for apt to be noninteractive
ENV DEBIAN_FRONTEND noninteractive
ENV DEBCONF_NONINTERACTIVE_SEEN true

RUN apt-get update \
 && apt-get install -yq --no-install-recommends \
    apt-utils \
    curl \
    sudo \
    git \
    zip \
    unzip \
    nano \
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

COPY db.php $MEICAN_DIR/config/

WORKDIR $MEICAN_DIR

RUN sed -i "s/MYSQL_DATABASE/$MYSQL_DATABASE/" $MEICAN_DIR/config/db.php \
 && sed -i "s/MYSQL_USER/$MYSQL_USER/" $MEICAN_DIR/config/db.php \
 && sed -i "s/MYSQL_PASSWORD/$MYSQL_PASSWORD/" $MEICAN_DIR/config/db.php \
 && chown meican:meican $MEICAN_DIR/config/db.php \
 && su meican -c "curl -kO https://getcomposer.org/composer.phar" \
 && su meican -c "php composer.phar global require "fxp/composer-asset-plugin:~1.4.4"" \
 && ln -s $MEICAN_DIR/web /var/www/meican \
 && a2enmod rewrite

COPY 000-default.conf /etc/apache2/sites-available/

EXPOSE 80

CMD su meican -c "php composer.phar install" \
 && service apache2 start \
 && /bin/bash