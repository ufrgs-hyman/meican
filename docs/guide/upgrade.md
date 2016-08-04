##UPGRADE GUIDE

####Version 1.x to 2.x

Upgrade is not possible. See the installation guide for a fresh install.

####Version 2.x to 3.x

Make every step detailed from [Migration guide](https://github.com/ufrgs-hyman/meican/blob/master/docs/guide/migration.md) option 2. After that, do more some steps to enable the Monitoring module:

- install OSCARS Bridge following [this guide](https://github.com/ufrgs-hyman/oscars-bridge/blob/master/README.md).
- configure the URL of the OSCARS Bridge in params.php
```
"oscars.bridge.provider.url" => 'http://localhost:8080/oscars-bridge/circuits',
```
- configure the URL of the Esmond service in params.php
```
"esmond.server.api.url" => 'http://localhost/esmond/v2/',
```
