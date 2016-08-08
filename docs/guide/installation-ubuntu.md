##INSTALLATION GUIDE - Ubuntu

Follow the steps detailed below.

This configuration was tested and performed on Ubuntu 14.04.

####Prepare environment

```
sudo apt-get update
sudo apt-get install apache2 mysql-server php5 curl php5-mysql php5-curl
```

####Setup database

Create a database via command line.

```
mysql -u #user# -p
CREATE DATABASE IF NOT EXISTS `meican2`;
```

####Download and install MEICAN

[Get a stable version](https://github.com/ufrgs-hyman/meican/releases):

```
wget https://github.com/ufrgs-hyman/meican/archive/#version#.tar.gz
tar -zxvf #version#.tar.gz
```

or clone the Git repository with the latest version (MAY BE NOT STABLE):

```
git clone https://github.com/ufrgs-hyman/meican.git
```

Configure database settings:

```
nano #meican-folder#/config/db.php
```

On source code folder (#meican-folder#) install the [Composer](https://getcomposer.org)

```
curl -sS https://getcomposer.org/installer | php
php composer.phar global require "fxp/composer-asset-plugin:~1.2.1"
```

Install MEICAN and all dependencies. It is **possible** that before the installation you are prompted by a "access token" of GitHub. You must have an account on [GitHub](https://github.com/settings/tokens) to request a valid token. 

In the root folder run:

```
php composer.phar install
```

Create a simbolic link to app web folder on /var/www:

```
sudo ln -s /path/to/#meican-folder#/web /var/www/meican
```

####Apache configuration

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

After that, MEICAN will be available at localhost with one user created:

```
user: master
pass: master
```

If you are doing a upgrade, go back to [Upgrade Guide](https://github.com/ufrgs-hyman/meican/blob/master/docs/guide/upgrade.md). If this is not your case, next step is set parameters and configure the app. Look the [Configuration Guide](https://github.com/ufrgs-hyman/meican/blob/master/docs/guide/configuration.md).
