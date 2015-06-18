# MEICAN 2 - Management Environment of Inter-domain Circuits for Advanced Networks

MEICAN is a network management environment that provides a service to request circuits in DCN (Dynamic Circuit Network). Users may access MEICAN through a graphical user interface based on Web 2.0 technologies, requesting the creation of circuits between the desired endpoints. In this process, users specify the source and destination points of the circuits, the bandwidth required, the time at which the circuit must be created, as well as the time interval in which the circuit must be active. The system also provides mechanisms that allow circuit requests to be provisioned automatically or upon the approval of network administrators. For this purpose, MEICAN internally employs a machine workflows with support for network management, which represent the operating policies set by the operators.

MEICAN 2 is a complete rewrite of its previous version. The system meets the demands of users contacting a Connection Service Provider with the Network Service Interface (NSI) protocol. In our environment, a central server MEICAN interacts with the Aggregator installed in the backbone of the Brazilian Research & Education Network (** RNP - Rede Nacional de Ensino e Pesquisa **). At RNP, MEICAN works as the central portal for all users who need to create circuits along its backbone.

##DIRECTORY STRUCTURE

```
assets/             global assets directory
certificates/       certificates used by application
components/         reused or third-party php scripts
config/             application configuration
controllers/        global controllers, e.g. RBAC
mail/               layouts and templates for mail sender
messages/           i18N translations
migrations/         database version control
models/             database models
modules/            application modules, e.g. circuits or topology
runtime/            folder for logging and debug features
tests/              test scripts
views/              global views, layouts or templates
web/                css, images, javascripts
```

##REQUIREMENTS

- Ubuntu 14.04 (or any other system with Crontab feature)
- Apache 2.4+ (recommended)
- MySQL 5+
- PHP 5.5+
- cURL

##INSTALLATION GUIDE

The following steps were performed on an Ubuntu 14.04.

#####Prepare environment

```
sudo apt-get install apache2 mysql-server php5 curl php5-mysql php5-curl
```

#####Setup database

While not mandatory, the phpMyAdmin installation is recommended for easy database management.

```
sudo apt-get install phpMyAdmin
```

Or you can simply create a database via the command line.

```
mysql -u #user# -p
CREATE DATABASE IF NOT EXISTS `meican2`;
```

#####Download and install MEICAN

Get source code:

```
wget https://github.com/ufrgs-hyman/meican2/archive/#version#.tar.gz
tar -zxvf #version#.tar.gz
```

Configure database settings:

```
nano meican2-#version#/config/db.php
```

On source code folder (meican2-#version#) install the [Composer](https://getcomposer.org)

```
curl -sS https://getcomposer.org/installer | php
php composer.phar global require "fxp/composer-asset-plugin:1.0.0”
```

Install MEICAN and all dependencies:

```
php composer.phar install
```

It is possible that before the installation you are prompted by a "access token" of GitHub. You must have a user on GitHub to get a valid token on: https://github.com/settings/tokens

Create a simbolic link to app web folder on /var/www:

```
sudo ln -s /path/to/meican2-#version#/web /var/www/meican
```

#####Apache configuration

Enable the Rewrite mode:

```
a2enmod rewrite
```

Enable symbol links and change the document root:

```
DocumentRoot /var/www/meican

<Directory /var/www/meican>
    Options Indexes FollowSymLinks MultiViews
    AllowOverride All
    Order deny,allow
    Allow from all
</Directory>
```
