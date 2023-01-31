# MEICAN

Management Environment of Inter-domain Circuits for Advanced Networks is a web application for the management of Dynamic Circuit Networks ([DCNs](https://en.wikipedia.org/wiki/Dynamic_circuit_network)). Users access MEICAN through a graphical user interface based on Web technologies, to requesting the creation of circuits between well defined endpoints. In this process, users specify the source and destination points of the circuits, the bandwidth required, the time at which the circuit must be created, as well as the time interval in which the circuit must be active. The system also provides mechanisms that allow circuit requests to be provisioned automatically or upon the approval of network administrators. For this purpose, MEICAN internally employs a machine workflows with support for network management, which represent the operating policies set by the operators.

Since version 2 the system meets the demands of users contacting a Connection Service Provider with the Network Service Interface (NSI) protocol. In our environment, a central server MEICAN interacts with the NSI Aggregator installed in the backbone of the Brazilian Research & Education Network (** RNP - Rede Nacional de Ensino e Pesquisa **). At RNP, MEICAN works as the central portal for all users who need to create circuits along its backbone.

Since version 3 the project has a better integration with monitoring systems. The circuits and networks can be monitored through of the same web application. Thus, it's unnecessary to access other systems to perform the monitoring of traffic in the circuits created by MEICAN.

This software is result of a partnership between the Brazilian Research & Education Network ([RNP](https://www.rnp.br)) and the Brazilian Federal University of Rio Grande do Sul ([UFRGS](http://www.ufrgs.br)).

Mobile friendly since version 3

![Alt text](/docs/mobile.png)

## DIRECTORY STRUCTURE

```
certificates/       	app certificates
config/             	app configurations
docker/			Docker files
mail/               	layouts and templates for mail sender
migrations/         	database version control
modules/            	application modules
aaa/			AAA Module
base/			Base Module
bpm/			BPM Module
circuits/		Circuits Module
home			Home Module
monitoring/         	Monitoring Module
nmwg/               	NMWG Module
notify/			Notify Module
nsi/                	NSI Module
perfsonar/          	PerfSONAR Module
oscars/			OSCARS Module
scheduler/		Scheduler Module
tester/             	Tester Module
topology/		Topology Module
	assets/		css, js files and bundle classes
	utils/		utils classes
	controllers/	containing controller class files
	forms/		form models for views
	messages/	I18N internationalization files
	models/		database or standard models, e.g., DAO classes
	views/		views and layout files
runtime/            	folder for logging and debug features
tests/              	test scripts
web/                	web accessible files, e.g., assets cache, wsdl files and images.
```

## REQUIREMENTS

### Hardware

- CPU 1+
- Memory 1GB+
- Storage 10GB+

### Software

- Ubuntu/CentOS/Any other OS with Crontab feature
- Apache 2.2+
- MySQL 5+ 
- PHP 5.5+
- cURL
- [OSCARS Bridge](https://github.com/ufrgs-hyman/oscars-bridge) (local or remote instance required by Monitoring Module)
- [ESnet Monitoring Daemon (Esmond)](https://github.com/esnet/esmond) (local or remote instance required by Monitoring Module)

## GUIDES

### Installation

Installation can be done using [Docker Compose](https://github.com/ufrgs-hyman/meican/blob/master/docs/guide/installation-docker-compose.md) or manually on [CentOS](https://github.com/ufrgs-hyman/meican/blob/master/docs/guide/installation-centos.md) or [Ubuntu](https://github.com/ufrgs-hyman/meican/blob/master/docs/guide/installation-ubuntu.md).

### Configuration

The documentation is [here](https://github.com/ufrgs-hyman/meican/blob/master/docs/guide/configuration.md).

### User guide

A very short documentation is available on Help section of the application. The complete user guide currently is available only in [portuguese](https://wiki.rnp.br/display/secipo/Guia+MEICAN).

### Migration

A complete migration of the application to another machine is detailed in this [guide](https://github.com/ufrgs-hyman/meican/blob/master/docs/guide/migration.md).

### Upgrade

Look [this document](https://github.com/ufrgs-hyman/meican/blob/master/docs/guide/upgrade.md) for instructions.

## LICENSE

Copyright (c) 2012-2023 by [RNP](https://www.rnp.br).
All rights reserved. MEICAN is released under of the BSD2 License. For more information see [LICENSE](https://github.com/ufrgs-hyman/meican/blob/master/LICENSE.md).
