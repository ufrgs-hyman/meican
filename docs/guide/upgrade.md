##UPGRADE GUIDE

####Version 1.x to 2.x

Upgrade is not possible. See the installation guide for a fresh install.

####Version 2.x to 3.x

####1. Migration

Perform every step detailed from option 2 of the [Migration guide](https://github.com/ufrgs-hyman/meican/blob/master/docs/guide/migration.md). After that, are required some steps to enable the Monitoring module:

####2. install OSCARS Bridge following [this guide](https://github.com/ufrgs-hyman/oscars-bridge/blob/master/README.md).

####3. Update parameters (Location: params.php in config folder)

####3.1. Configure the URL of the OSCARS Bridge in params.php

After this line:
```
"provider.force.dummy" => true,
```
Add this:
```
"oscars.bridge.provider.url" => 'http://localhost:8080/oscars-bridge/circuits',
```

####4. configure the URL of the Esmond REST API in params.php

After the line added above, add this other:
```
"esmond.server.api.url" => 'http://localhost/esmond/v2/',
```

Done! Your application must be ready for use.
