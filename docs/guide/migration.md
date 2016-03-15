##MIGRATION GUIDE

This guide covers the migration of the application to another machine.

#####1. Backup of the configurations

Backup the following files from config`s folder: "db.php", "mailer.php" and "params.php".

#####2. Backup of the database

Access the phpMyAdmin interface and export the MEICAN database.

#####3. Prepare the new environment

Following the installation guide respective, setup a complete new MEICAN instance on the new machine.

#####4. Copy the configuration

From the backup, copy the configuration to the "config" folder.

#####5. Import the database

From the backup, import the database directly on phpMyAdmin.

After all these steps, the migration is finished.