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
- Storage 20GB+

###Software

- Ubuntu 14.04/CentOS 6.7/Any other OS with Crontab feature
- Apache 2.2+ (recommended)
- MySQL 5+
- PHP 5.5+
- cURL

##INSTALLATION GUIDE

There is two specific installation guides, one for [CentOS 6.7](https://github.com/ufrgs-hyman/meican2/wiki/CentOS-6.7-installation-guide) and another for [Ubuntu 14.04](https://github.com/ufrgs-hyman/meican2/wiki/Ubuntu-14.04-installation-guide).

##CONFIGURATION GUIDE

The documentation is [here](https://github.com/ufrgs-hyman/meican2/wiki/CentOS-6.7-installation-guide).
