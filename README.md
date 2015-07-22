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

###Hardware

- CPU 1+
- Memory 2GB+
- Storage 25GB+

###Software

- Ubuntu 14.04 (or any other system with Crontab feature)
- Apache 2.4+ (recommended)
- MySQL 5+
- PHP 5.5+
- cURL

##INSTALLATION GUIDE

####Option 1

[Download a specific release](https://github.com/ufrgs-hyman/meican2/releases) and use the MEICAN Installer for Ubuntu (tested on 14.04).

```
wget https://github.com/ufrgs-hyman/meican2/archive/#version#.tar.gz
tar -zxvf #version#.tar.gz
cd meican2-#version#
chmod +x installer.sh
sudo ./installer.sh
```

####Option 2

Follow the steps detailed below.

This configuration was tested and performed on Ubuntu 14.04.

#####Prepare environment

```
sudo apt-get update
sudo apt-get install apache2 mysql-server php5 curl php5-mysql php5-curl
```

#####Setup database

While not mandatory, the phpMyAdmin installation is recommended for easy database management.

```
sudo apt-get install phpmyadmin
```

Or you can simply create a database via the command line.

```
mysql -u #user# -p
CREATE DATABASE IF NOT EXISTS `meican2`;
```

#####Download and install MEICAN

[Get a stable version](https://github.com/ufrgs-hyman/meican2/releases):

```
wget https://github.com/ufrgs-hyman/meican2/archive/#version#.tar.gz
tar -zxvf #version#.tar.gz
```

or clone the Git repository with the latest version (MAY BE NOT STABLE):

```
git clone https://github.com/ufrgs-hyman/meican2.git
```

Configure database settings:

```
nano #meican-folder#/config/db.php
```

On source code folder (#meican-folder#) install the [Composer](https://getcomposer.org)

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
sudo ln -s /path/to/#meican-folder#/web /var/www/meican
```

#####Apache configuration

Enable the Rewrite mode:

```
sudo a2enmod rewrite
```

Enable symbolic links and change the document root:

```
DocumentRoot /var/www/meican

<Directory /var/www>
    Options Indexes FollowSymLinks MultiViews
    AllowOverride All
    Order deny,allow
    Allow from all
</Directory>
```

Finally restart the Apache service:

```
sudo service apache2 restart
```

With this configuration, MEICAN will be available at http://localhost with one user created:

```
user: master
pass: master
```

###PARAMETERS

Location: config/params.php

We need configure the NSA ID to identify MEICAN to other NSI providers:

```
'meican.nsa.id' => "urn:ogf:network:#DOMAIN#:#YEAR#:nsa:meican",
```

Located on certificates folder on project root, the application certificate must be setup also:

```
'meican.certificate.filename' => "meican.pem",
'meican.certificate.passphrase' => "#CERTIFICATE-PASSWORD#",	
```

By default the fake provider is enabled. Disable this feature setting the param below:

```
"provider.force.dummy" => false,
```
