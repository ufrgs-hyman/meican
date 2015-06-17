# MEICAN 2 - Management Environment of Inter-domain Circuits for Advanced Networks

MEICAN is a network management environment that provides a service to request circuits in DCN (Dynamic Circuit Network). Users may access MEICAN through a graphical user interface based on Web 2.0 technologies, requesting the creation of circuits between the desired endpoints. In this process, users specify the source and destination points of the circuits, the bandwidth required, the time at which the circuit must be created, as well as the time interval in which the circuit must be active. The system also provides mechanisms that allow circuit requests to be provisioned automatically or upon the approval of network administrators. For this purpose, MEICAN internally employs a machine workflows with support for network management, which represent the operating policies set by the operators.

MEICAN 2 is a complete rewrite of its previous version. The system meets the demands of users contacting a Connection Service Provider with the Network Service Interface (NSI) protocol. In our environment, a central server MEICAN interacts with the Aggregator installed in the backbone of the Brazilian Research & Education Network (** RNP - Rede Nacional de Ensino e Pesquisa **). At RNP, MEICAN works as the central portal for all users who need to create circuits along its backbone.

##DIRECTORY STRUCTURE

##REQUIREMENTS

PHP 5.5+

##INSTALLATION GUIDE

###Apache configuration

```<Directory /var/www/meican>
                Options Indexes FollowSymLinks MultiViews
                AllowOverride All
                Order deny,allow
                Allow from all
        </Directory>```
