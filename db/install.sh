apt-get update
apt-get install -y apache2 mysql-server mysql-client php5 php5-mysql php-apc phpmyadmin php-pear subversion ntp

# Configurando mysql-server: selecionar senha de para usuário “root”
# Configurando phpmyadmin: 
#	- Selecionar servidor web: apache2
#	- Configurar base com dbconfig-common? sim
#	- Palavra-passe do administrador: definida anteriormente na configuração do mysql
#	- Palavra-passe da aplicação Mysql

pear install Mail
pear install Net_SMTP
a2enmod rewrite #libera mod_rewrite no apache

#mkdir /home/www
#chmod 755 /home/www/
cd /var/www #download do svn
svn checkout https://svn-redes.inf.ufrgs.br/hyman/tags/meican_v2-2 meican-main
#svn checkout https://svn-redes.inf.ufrgs.br/hyman/trunks/meican meican-trunk
ln -ns /var/www/meican-main/ /var/www/meican

chown -R www-data:www-data /var/www/meican-main/log

cp /var/www/meican-main/db/meican.conf /etc/apache2/conf.d/ #Colocar configuração do meican no apache


nano /etc/apache2/sites-available/default #colocar /var/www/meican como document root

/etc/init.d/apache2 restart

nano /var/www/meican-main/db/build.sh #configurar usuário e senha do mysql no script de importação do banco

cd /var/www/meican-main/db/
bash build.sh

nano /var/www/meican-main/config/local.php #editar arquivo de configuração do meican
