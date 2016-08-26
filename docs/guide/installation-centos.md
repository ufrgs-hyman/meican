##INSTALLATION GUIDE - CentOS

Follow the steps detailed below.

This configuration was tested and performed on CentOS 6.7.

####1. Prepare environment

####1.1. Update Yum repository
```
yum update
rpm -Uvh https://mirror.webtatic.com/yum/el6/latest.rpm
```
####1.2. Install Apache
```
yum install httpd
```

####1.3. Install MySQL
```
yum install mysql-server
```

####1.4. Install cURL
```
yum install curl
```

####1.5. Install PHP 5.5

```
yum install php55w php55w-mysql php55w-curl php55w-soap php55w-xml php55w-mbstring
```

There may be a conflict between the native PHP CentOS and the version that the Meican requires. To remove the native version, run:

```
yum remove php-common
```

After that, runs the install command again.

####1.6. Install and start OSCARS Bridge

See [this document](https://github.com/ufrgs-hyman/oscars-bridge/blob/master/README.md) for instructions.

####1.7. Start services

Turn on all required services: 

````
chkconfig mysqld on
service mysqld start
chkconfig httpd on
service httpd start
````

####1.8. Firewall configuration

In the RNP environment, the server is protected by an outer firewall and its rules are controlled at a higher level. The firewall of the operating system level is not required for this environment, but it can be to another. To disable the firewall perform the following:

```
service iptables save
service iptables stop
chkconfig iptables off
```

####1.8. SELinux configuration

MEICAN requires the permissive mode of SELinux. 

```
setenforce 0
```

####2. Setup database

If this is an **upgrade**, you don't need create a database. Go to step 3.

The fastest method to do this is via command line:

```
mysql -u #user# -p
CREATE DATABASE IF NOT EXISTS `meican`;
```

####3. Download and install MEICAN

####3.1. Download

In a public folder (e.g. /var/www) [download a stable version](https://github.com/ufrgs-hyman/meican/releases):

```
wget https://github.com/ufrgs-hyman/meican/archive/#version#.tar.gz
tar -zxvf #version#.tar.gz
```

####3.2. Configure database settings

**Warning**: If this is an **upgrade**, you must keep the old settings and the new installation will be upgrade the database keeping the data. Look the older db.php file and copy the database name and credentials.

```
nano #meican-folder#/config/db.php
```

####3.3. Install Composer

On source code folder (#meican-folder#) install the [Composer](https://getcomposer.org)

```
curl -O https://getcomposer.org/download/1.2.0/composer.phar
php composer.phar global require "fxp/composer-asset-plugin:~1.2.1"
```

####3.4. Install MEICAN

Install MEICAN and all dependencies. It is **possible** that before the installation you are prompted by a "access token" of GitHub. You must have an account on [GitHub](https://github.com/settings/tokens) to request a valid token. 

In the meican folder run:

```
php composer.phar install
```

####4. Apache configuration

Create a simbolic link to app web folder on /var/www:

```
sudo ln -s /path/to/#meican-folder#/web /var/www/meican
```

By default, the Rewrite mode is enabled on CentOS 6.7. To confirm this, verify that the following line is uncommented:

```
LoadModule rewrite_module modules/mod_rewrite.so
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
service httpd restart
```

After that, MEICAN will be available at localhost with one user created:

```
user: master
pass: master
```

If you are doing a upgrade, go back to [Upgrade Guide](https://github.com/ufrgs-hyman/meican/blob/master/docs/guide/upgrade.md). If this is not your case, next step is set parameters and configure the app. Look the [Configuration Guide](https://github.com/ufrgs-hyman/meican/blob/master/docs/guide/configuration.md).
