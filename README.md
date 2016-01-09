# MEICAN 2 - Management Environment of Inter-domain Circuits for Advanced Networks

MEICAN is a network management environment that provides a service to request circuits in DCN (Dynamic Circuit Network). Users may access MEICAN through a graphical user interface based on Web 2.0 technologies, requesting the creation of circuits between the desired endpoints. In this process, users specify the source and destination points of the circuits, the bandwidth required, the time at which the circuit must be created, as well as the time interval in which the circuit must be active. The system also provides mechanisms that allow circuit requests to be provisioned automatically or upon the approval of network administrators. For this purpose, MEICAN internally employs a machine workflows with support for network management, which represent the operating policies set by the operators.

MEICAN 2 is a complete rewrite of its previous version. The system meets the demands of users contacting a Connection Service Provider with the Network Service Interface (NSI) protocol. In our environment, a central server MEICAN interacts with the Aggregator installed in the backbone of the Brazilian Research & Education Network (** RNP - Rede Nacional de Ensino e Pesquisa **). At RNP, MEICAN works as the central portal for all users who need to create circuits along its backbone.

This software is result of a partnership between the Brazilian Research & Education Network ([RNP](http://www.rnp.br)) and the Brazilian Federal University of Rio Grande do Sul ([UFRGS](http://www.ufrgs.br)).

##DIRECTORY STRUCTURE

```
certificates/       	app certificates
config/             	app configurations
mail/               	layouts and templates for mail sender
migrations/         	database version control
modules/            	application modules
	aaa/				AAA Module
	base/				Base Module
	bpm/				BPM Module
	circuits/			Circuits Module
	home				Home Module
	notification/		Notification Module
	scheduler/			Scheduler Module
	topology/			Topology Module
		assets/			assets classes and their css and js files
		components/		independent submodules or third part classes
		controllers/	containing controller class files
		forms/			form models for views
		messages/		I18N internationalization files
		models/			database or standard models, e.g., DAO classes
		views/			views and layout files
runtime/            folder for logging and debug features
tests/              test scripts
web/                web accessible files, e.g., assets cache, wsdl files and images.
```

##REQUIREMENTS

###Hardware

- CPU 1+
- Memory 2GB+
- Storage 20GB+

###Software

- Ubuntu 14/CentOS 6/Any other OS with Crontab feature
- Apache 2.2+ (recommended)
- MySQL 5+
- PHP 5.5+
- cURL

##GUIDES

###Installation

There is two specific installation guides, one for [CentOS 6](https://github.com/ufrgs-hyman/meican2/blob/master/docs/guide/installation-centos.md) and another for [Ubuntu 14](https://github.com/ufrgs-hyman/meican2/blob/master/docs/guide/installation-ubuntu.md).

###Configuration

The documentation is [here](https://github.com/ufrgs-hyman/meican2/blob/master/docs/guide/configuration.md).

###User guide

A very short documentation is available on Help section of the application. The complete user guide currently is available only in [portuguese](https://wiki.rnp.br/display/secipo/Guia+MEICAN).

##LICENSE

Copyright (c) 2012-2016 by [RNP](http://www.rnp.br).
All rights reserved. MEICAN is released under of the BSD2 License. For more information see [LICENSE](https://github.com/ufrgs-hyman/meican2/blob/master/LICENSE.md).
