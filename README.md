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

####[CentOS 6.7](https://github.com/ufrgs-hyman/meican2/wiki/CentOS-6.7-installation-guide)

####[Ubuntu 14.04](https://github.com/ufrgs-hyman/meican2/wiki/Ubuntu-14.04-installation-guide)

After that installation, MEICAN will be available at localhost with one user created:

```
user: master
pass: master
```

Next step is set some parameters and configure the application.

###PARAMETERS

Location: config/params.php

Located on certificates folder on project root, the application certificate must be defined:

```
'meican.certificate.filename' => 'meican.pem',
'meican.certificate.passphrase' => '#CERTIFICATE-PASSWORD#',	
```

By default the fake provider is enabled. Disable this feature setting the param below:

```
'provider.force.dummy' => false,
```

For the pass recovery form, the application uses the [Google reCAPTCHA API](https://www.google.com/recaptcha). The keys must be set:

```
'google.recaptcha.secret.key' => '',
'google.recaptcha.site.key' => '',
```

The feedback system requires a valid source email, destination email and a valid SMTP server:

```
'mailer.source' => 'meican@inf.ufrgs.br',
'mailer.destination' => 'meican@inf.ufrgs.br',
```

The SMTP server is defined in a separated file. It requires a host, user and password.

Location: config/mailer.php

```
'class' => 'yii\swiftmailer\Mailer',
'transport' => [
    'class' => 'Swift_SmtpTransport',
    'host' => '',
    'username' => '',
    'password' => '',
    'port' => '465',
    'encryption' => 'ssl',
],
```

###CONFIGURATION

We need set the MEICAN NSA ID to identify that application by other NSI providers. Access the application and enter to Reservations > Configuration and set the MEICAN NSA ID field with a valid id:

```
urn:ogf:network:#DOMAIN#:#YEAR#:nsa:meican
```

If the fake provider has been disabled, we need to define a true provider to receive the MEICAN requests. On the same configuration page (Reservations > Configuration) you can set the Provider NSA ID and the URL of the Connection Service.
