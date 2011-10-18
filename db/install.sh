apt-get update
apt-get install -y apache2 mysql-server mysql-client php5 php5-mysql php-apc phpmyadmin php-pear subversion ntp

pear install mdb2
pear install pear/MDB2#mysql
pear install Mail
pear install Net_SMTP

#mkdir /home/www
#chmod 755 /home/www/
cd /var/www
svn checkout https://svn-redes.inf.ufrgs.br/hyman/branches/meican2706/ meican-2706
svn checkout https://svn-redes.inf.ufrgs.br/hyman/trunks/meican meican-main
ln -ns /var/www/meican-main/ /var/www/meican
ln -s /var/www/meican-main/db/meican.conf /etc/apache2/conf.d/

chown -R www-data:www-data /var/www/meican-main/log
chown -R www-data:www-data /var/www/meican-2706/log

nano /etc/apache2/sites-available/default

/etc/init.d/apache2 restart