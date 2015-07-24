<?php

use yii\db\Schema;
use yii\db\Migration;

class m150626_171138_diego extends Migration
{
    public function up()
    {
    	$this->execute("
DELETE FROM `meican_notification`;
		");
    	 
    	$this->execute("
ALTER TABLE `meican_notification` CHANGE `type` `type` ENUM('AUTHORIZATION','TOPOLOGY','RESERVATION','NOTICE') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
		");
    	
    	$this->execute("
ALTER TABLE `meican_notification` ADD INDEX(`type`);
    	");
    	
    	$this->execute("
ALTER TABLE `meican_notification` ADD INDEX(`user_id`);
    	");
    	
    }

    public function down()
    {
        echo "m150626_171138_diego cannot be reverted.\n";

        return false;
    }
}
