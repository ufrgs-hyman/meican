##MIGRATION GUIDE

This guide covers the migration of the application to another machine.

#####1. Backup of the certficate

Your new server will have the same domain? If yes, you need a backup of the certficate.

#####2. Backup of the configurations

Backup the following files from config`s folder: "db.php", "mailer.php" and "params.php". 

#####3. Backup of the database

Access the phpMyAdmin interface and export the MEICAN database. By default the database name is "meican2".

#####4. Prepare the new environment

Following the installation guide respective, setup a complete new MEICAN instance on the new machine.

#####5. Copy the certificate

From the backup, copy the certificate to the certificate`s folder on the new server.

#####6. Copy the configuration

From the backup, copy all files to the config`s folder on the new server.

#####7. Import the database

From the backup, import the database directly on phpMyAdmin.

After all these steps, the migration is done.

Test your application accessing http://localhost
