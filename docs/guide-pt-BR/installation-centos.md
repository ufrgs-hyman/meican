##GUIA DE INSTALAÇÃO - CentOS

Siga as etapas detalhadas a seguir.

Esta configuração foi testada e realizada no CentOS 6.7.

#####Preparar o ambiente

```
yum update
rpm -Uvh https://mirror.webtatic.com/yum/el6/latest.rpm
yum install httpd mysql-server php55w curl php55w-mysql php55w-curl
```

O CentOS geralmente já possui uma versão do PHP previamente instalada, entretanto esta versão pode não ser compatível com o MEICAN. Por isso, é necessário remover a antiga versão e instalar a 5.5 ou superior. Para remover execute:

```
yum remove php-common
```

Após a remoção é possível tentar instalar a versão correta:

```
yum install php55w php55w-mysql php55w-curl
```

Ative os serviços recém instalados:

````
chkconfig mysqld on
service mysqld start
chkconfig httpd on
service httpd start
````

No ambiente da RNP, o servidor no qual o MEICAN está alocado é protegido por um firewall externo e suas regras são controladas em alto nível. Dessa forma o firewall local não é necessário no ambiente da RNP. Cabe ao administrador da rede local esta decisão. Para desabilitar o firewall execute o seguinte:

```
service iptables save
service iptables stop
chkconfig iptables off
```

#####Configurar o banco de dados

Mesmo que não obrigatório, a instalação do phpMyAdmin é recomendada para gerenciamento dos dados persistidos de forma facilitada. Para instalar execute:

```
yum install epel-release
yum install phpmyadmin
```

Você pode criar a base de dados a partir da interface gráfica do phpMyAdmin ou executar os seguintes comandos no terminal:

```
mysql -u #user# -p
CREATE DATABASE IF NOT EXISTS `meican2`;
```

#####Download e instalação do MEICAN

Em uma pasta de acesso permitido ao usuário apache (e.g. /var/www) [baixe uma versão estável](https://github.com/ufrgs-hyman/meican2/releases) (recomendado):

```
wget https://github.com/ufrgs-hyman/meican2/archive/#version#.tar.gz
tar -zxvf #version#.tar.gz
```

ou, você pode efetuar o download da última versão (TALVEZ NÃO ESTÁVEL) do sistema diretamente do repositório GitHub:

```
git clone https://github.com/ufrgs-hyman/meican2.git
```

Agora precisamos definir junto ao MEICAN as configurações do banco de dados local. Para isso deve-se acessar o seguinte arquivo:

```
nano #meican-folder#/config/db.php
```

As dependências do projeto são mantidas a partir do [Composer](https://getcomposer.org). Na raiz do projeto (#meican-folder#) execute os seguintes comandos: 

```
curl -sS https://getcomposer.org/installer | php
php composer.phar global require "fxp/composer-asset-plugin:~1.1.2"
```

Com o Composer instalado, podemos prosseguir para o download das dependências. Ainda na raiz do projeto, execute:

```
php composer.phar install
```

É possível que durante o processo de instalação seja solicitado um token de acesso ou "access token" fornecido pelo GitHub. Para continuar, você terá que acessar sua conta no GitHub e solicitar um token de acesso na página de tokens pessoais: https://github.com/settings/tokens

Ao final da instalação das dependências podemos passar para a configuração de acesso a interface web.

Primeiro, é necessário criar um link simbólico em "/var/www" para a pasta pública do projeto "web":

```
sudo ln -s /path/to/#meican-folder#/web /var/www/meican
```

#####Configuração do Apache

Por padrão, o Rewrite mode está ativado no CentOS 6.7. Para confirmar isso verifique se a seguinte linha está descomentada no arquivo de configuração do Apache:

```
LoadModule rewrite_module modules/mod_rewrite.so
```

Ative os links simbólicos e altere o DocumentRoot como definido abaixo:

```
DocumentRoot /var/www/meican

<Directory /var/www>
    Options Indexes FollowSymLinks MultiViews
    AllowOverride All
    Order deny,allow
    Allow from all
</Directory>
```

Por fim, reinicie o Apache para confirmar as alterações:

```
service httpd restart
```

Após isso, o MEICAN estará disponível em http://localhost com o seguinte usuário criado:

```
user: master
pass: master
```

Este é o fim do Guia de Instalação. A próxima etapa é definir alguns parâmetros e configurar a aplicação. Veja o [Guia de Configuração](https://github.com/ufrgs-hyman/meican2/blob/master/docs/guide-pt-BR/configuration.md).
