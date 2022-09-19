## MEICAN Docker

### Requirements:
* <a href="https://docs.docker.com/install">Docker</a>
* <a href="https://docs.docker.com/compose/install">Docker Compose</a>


### Instructions to run:


1- In the root directory, open the file'.env' to configure your credentials;

   - You must configure the following items:
   
     * MYSQL_ROOT_PASSWORD
    
     * MYSQL_DATABASE
    
     * MYSQL_USER
    
     * MYSQL_PASSWORD
     
     * MEICAN_PORT
     
     * MEICAN_VERSION
        
        
2- Run the following command in the root directory: (Choose between development or production mode)

  - For Development mode: ```docker-compose -f docker-compose.yml -f docker-compose.dev.yml -p meican up --build```
  - For Production mode: ```docker-compose -f docker-compose.yml -f docker-compose.prod.yml -p meican up --build```
  


3- After, MEICAN will be available at localhost in port configured using the `MEICAN_PORT` parameter, with one user created:

```
user: master
pass: master
```

If you are doing a upgrade, go back to [Upgrade Guide](https://github.com/ufrgs-hyman/meican/blob/master/docs/guide/upgrade.md). If this is not your case, next step is set parameters and configure the app. Look the [Configuration Guide](https://github.com/ufrgs-hyman/meican/blob/master/docs/guide/configuration.md).

