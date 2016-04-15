<?php
/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

use yii\db\Schema;
use yii\db\Migration;

class m150618_203039_diego extends Migration
{
    public function up()
    {
    	$this->execute("
DELETE FROM `meican_bpm_workflow`;
		");
    	
    	$this->execute("
ALTER TABLE `meican_bpm_workflow` DROP FOREIGN KEY `bpm_workflow_domain`;
		");
    	
    	$this->execute("
ALTER TABLE `meican_bpm_workflow` DROP `domain_id`;
		");
    	
    	$this->execute("
ALTER TABLE `meican_bpm_workflow` ADD `domain` VARCHAR(50) NOT NULL AFTER `name`;
		");

    	$this->execute("
DELETE FROM `meican_connection_auth`;
		");
    	
    	$this->execute("
ALTER TABLE `meican_connection_auth` DROP FOREIGN KEY `manager_domain`;
		");
    	
    	$this->execute("
ALTER TABLE `meican_connection_auth` DROP `domain_id`;
		");
    	
    	$this->execute("
ALTER TABLE `meican_connection_auth` ADD `domain` VARCHAR(50) NOT NULL AFTER `id`;
		");
    	
    	$this->execute("
DELETE FROM `meican_bpm_flow_control`;
		");
    	
    	$this->execute("
ALTER TABLE `meican_bpm_flow_control` DROP FOREIGN KEY `bpm_flow_domain`;
		");
    	
    	$this->execute("
ALTER TABLE `meican_bpm_flow_control` DROP `domain_id`;
		");
    	
    	$this->execute("
ALTER TABLE `meican_bpm_flow_control` ADD `domain` VARCHAR(50) NOT NULL AFTER `workflow_id`;
		");

    	$this->execute("
CREATE TABLE `meican_notification` ( `id` INT NOT NULL AUTO_INCREMENT , `user_id` INT NOT NULL , `date` DATE NOT NULL , `type` ENUM('AUTHORIZATION','TOPOLOGY','RESERVATION') NOT NULL , `viewed` BOOLEAN NOT NULL , `info` VARCHAR(200) NOT NULL , PRIMARY KEY (`id`) ) ENGINE = InnoDB;
		");
    	
    	$this->execute("
ALTER TABLE `meican_notification` ADD CONSTRAINT `meican_notification_user` FOREIGN KEY (`user_id`) REFERENCES `meican_user`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;
		");

    }

    public function down()
    {
        echo "m150618_203039_diego cannot be reverted.\n";

        return false;
    }
    
  
}
