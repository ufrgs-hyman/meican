##INSTALLATION GUIDE - CentOS

Follow the steps detailed below.

This configuration was tested and performed on CentOS 6.7.

#####Prepare environment

```
yum update
rpm -Uvh https://mirror.webtatic.com/yum/el6/latest.rpm
yum install httpd mysql-server php55w curl php55w-mysql php55w-curl php55w-soap php55w-xml
```

There may be a conflict between the native PHP CentOS and the version that the Meican requires. To remove the native version, run:

```
yum remove php-common
```

After that, runs the install again.

Turn on all required services: 

````
chkconfig mysqld on
service mysqld start
chkconfig httpd on
service httpd start
````

In the RNP environment, the server is protected by an outer firewall and its rules are controlled at a higher level. The firewall of the operating system level is not required for this environment, but it can be to another. To disable the firewall perform the following:

```
service iptables save
service iptables stop
chkconfig iptables off
```

#####Setup database

While not mandatory, the phpMyAdmin installation is recommended for easy database management.

```
yum install epel-release
yum install phpmyadmin
```

Or you can simply create a database via command line.

```
mysql -u #user# -p
CREATE DATABASE IF NOT EXISTS `meican2`;
```

#####Download and install MEICAN

In a public folder (e.g. /var/www) [download a stable version](https://github.com/ufrgs-hyman/meican/releases):

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

#####Apache configuration

By default, the Rewrite mode is enabled on CentOS 6.7. To confirm this verify that the following line is uncommented:

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

Next step is set parameters and configure the app. Look the [Configuration Guide](https://github.com/ufrgs-hyman/meican/blob/master/docs/guide/configuration.md).
