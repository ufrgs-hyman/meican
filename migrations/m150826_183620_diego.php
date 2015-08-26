<?php

use yii\db\Schema;
use yii\db\Migration;

class m150826_183620_diego extends Migration
{
    public function up()
    {
    	$this->execute("
   			DELETE FROM `meican_bpm_flow_control`;
        ");
    	
    	$this->execute("
    		ALTER TABLE `meican_bpm_flow_control` CHANGE `domain` `domain` VARCHAR(60) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL;
        ");
    	
    	$this->execute("
            ALTER TABLE `meican_bpm_flow_control` ADD INDEX(`domain`);
        ");
    	
    	$this->execute("
    		ALTER TABLE `meican_bpm_flow_control` ADD CONSTRAINT `bpm_flow_domain` FOREIGN KEY (`domain`) REFERENCES `meican2`.`meican_domain`(`name`) ON DELETE CASCADE ON UPDATE CASCADE;
        ");
    	
    	$this->execute("
   			DELETE FROM `meican_bpm_workflow`;
    	");
    	
    	$this->execute("
   			ALTER TABLE `meican_bpm_workflow` CHANGE `domain` `domain` VARCHAR(60) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL;
        ");
    	
    	$this->execute("
   			ALTER TABLE `meican_bpm_workflow` ADD INDEX(`domain`);
        ");
    	
    	$this->execute("
   			ALTER TABLE `meican_bpm_workflow` ADD CONSTRAINT `bpm_workflow_domain` FOREIGN KEY (`domain`) REFERENCES `meican2`.`meican_domain`(`name`) ON DELETE CASCADE ON UPDATE CASCADE;
        ");
    	
    	$this->execute("
  			DELETE FROM `meican_connection_auth`;
        ");
    	
    	$this->execute("
  			ALTER TABLE `meican_connection_auth` CHANGE `domain` `domain` VARCHAR(60) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL;
        ");
    	
    	$this->execute("
  			ALTER TABLE `meican_connection_auth` ADD INDEX(`domain`);
        ");
    	
    	$this->execute("
  			ALTER TABLE `meican_connection_auth` ADD CONSTRAINT `manager_domain` FOREIGN KEY (`domain`) REFERENCES `meican2`.`meican_domain`(`name`) ON DELETE CASCADE ON UPDATE CASCADE;
        ");
    	
    	$this->execute("
  			ALTER TABLE `meican_user_domain` CHANGE `domain` `domain` VARCHAR(60) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL;
        ");
    	
    	$this->execute("
    		ALTER TABLE `meican_user_domain` ADD INDEX(`domain`);
        ");
    	
    	$this->execute("
    		ALTER TABLE `meican_user_domain` ADD CONSTRAINT `user_domain_domain` FOREIGN KEY (`domain`) REFERENCES `meican2`.`meican_domain`(`name`) ON DELETE CASCADE ON UPDATE CASCADE;
        ");
    }

    public function down()
    {
        echo "m150826_183620_diego cannot be reverted.\n";

        return false;
    }
    
    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }
    
    public function safeDown()
    {
    }
    */
}
