##GUIA DE INSTALAÇÃO - Ubuntu

Siga as etapas detalhadas a seguir.

Esta configuração foi testada e realizada no Ubuntu 14.04.

#####Preparar o ambiente

```
sudo apt-get update
sudo apt-get install apache2 mysql-server php5 curl php5-mysql php5-curl
```

#####Configurar o banco de dados

Mesmo que não obrigatório, a instalação do phpMyAdmin é recomendada para gerenciamento dos dados persistidos de forma facilitada. Para instalar execute:

```
sudo apt-get install phpmyadmin
```

Você pode criar a base de dados a partir da interface gráfica do phpMyAdmin ou executar os seguintes comandos no terminal:

```
mysql -u #user# -p
CREATE DATABASE IF NOT EXISTS `meican2`;
```

#####Download e instalação do MEICAN

[Efetue o download de uma versão estável](https://github.com/ufrgs-hyman/meican/releases) (recomendado):

```
wget https://github.com/ufrgs-hyman/meican/archive/#version#.tar.gz
tar -zxvf #version#.tar.gz
```

ou, você pode efetuar o download da última versão (TALVEZ NÃO ESTÁVEL) do sistema diretamente do repositório GitHub:

```
git clone https://github.com/ufrgs-hyman/meican.git
```

Agora precisamos definir junto ao MEICAN as configurações do banco de dados local. Para isso deve-se acessar o seguinte arquivo:

```
nano #meican-folder#/config/db.php
```

As dependências do projeto são mantidas a partir do [Composer](https://getcomposer.org). Na raiz do projeto (#meican-folder#) execute os seguintes comandos: 

```
curl -sS https://getcomposer.org/installer | php
php composer.phar global require "fxp/composer-asset-plugin:~1.1.4"
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

Ative o Rewrite Mode executando:

```
sudo a2enmod rewrite
```

Acesse o arquivo de configuração (000-default.conf) e ative os links simbólicos e altere o DocumentRoot como definido abaixo:

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
sudo service apache2 restart
```

Após isso, o MEICAN estará disponível em http://localhost com o seguinte usuário criado:

```
user: master
pass: master
```

Este é o fim do Guia de Instalação. A próxima etapa é definir alguns parâmetros e configurar a aplicação. Veja o [Guia de Configuração](https://github.com/ufrgs-hyman/meican/blob/master/docs/guide-pt-BR/configuration.md).
