## MEICAN Docker

### Requirements:
* <a href="https://docs.docker.com/install">Docker</a>
* <a href="https://docs.docker.com/compose/install">Docker Compose</a>


### Instructions to run:


1- Enter the directory and open the file .env to configure your credentials;

   - You must configure the following items:
   
     * MYSQL_ROOT_PASSWORD
    
     * MYSQL_DATABASE
    
     * MYSQL_USER
    
     * MYSQL_PASSWORD
     
     * MEICAN_PORT
     
     * MEICAN_VERSION
        
        
2- Run the following command:

    docker-compose up


3- After, MEICAN will be available at localhost in port previously configured.
