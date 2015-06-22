<?php

use yii\db\Schema;
use yii\db\Migration;

class m150622_190352_diego extends Migration
{
    public function up()
    {
    	$this->execute("
DELETE FROM `meican_notification`;
		");
    	
    	$this->execute("
ALTER TABLE `meican_notification` CHANGE `date` `date` DATETIME NOT NULL;
		");
    	
    }

    public function down()
    {
        echo "m150622_190352_diego cannot be reverted.\n";

        return false;
    }
    
}
