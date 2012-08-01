apt-get update
apt-get install -y apache2 mysql-server mysql-client php5 php5-mysql php-apc phpmyadmin php-pear subversion ntp

# Configurando mysql-server: selecionar senha de para usuário “root”
# Configurando phpmyadmin: 
#	- Selecionar servidor web: apache2
#	- Configurar base com dbconfig-common? sim
#	- Palavra-passe do administrador: definida anteriormente na configuração do mysql
#	- Palavra-passe da aplicação Mysql:

pear install Mail
pear install Net_SMTP

a2enmod rewrite #libera mod_rewrite no apache
a2enmod ssl #habilita o modulo ssl

cd /var/www #download do svn
svn checkout https://svn-redes.inf.ufrgs.br/hyman/tags/meican_v2-3
#svn checkout https://svn-redes.inf.ufrgs.br/hyman/trunks/meican meican-trunk
ln -ns /var/www/meican_v2-3 /var/www/meican

chown -R www-data:www-data /var/www/meican/log

cd /etc/apache2/sites-available
ln -s /var/www/meican/apache.conf meican.conf #Colocar configuração do meican no apache
cd /etc/apache2/sites-enabled
ln -s ../sites-available/meican.conf meican.conf

nano /etc/apache2/sites-available/meican.conf #configurar site do meican no apache
nano /var/www/meican/.htaccess #configurar htaccess, descomentar linhas necessárias se optar pelo redirecionamento para porta 443

/etc/init.d/apache2 restart

cd /var/www/meican/db/
nano build.sh #configurar usuário e senha do mysql no script de importação do banco
bash build.sh

nano /var/www/meican/config/local.php #editar arquivo de configuração do meican