<?php

use yii\db\Schema;
use yii\db\Migration;

class m150713_162423_diego extends Migration
{
	public function up()
	{
    	 
    	$this->execute("
ALTER TABLE `meican_connection` CHANGE `auth_status` `auth_status` ENUM('EXPIRED','WAITING','AUTHORIZED','DENIED','UNEXECUTED','UNSOLICITED') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
		");
	    	
    }

    public function down()
    {
        echo "m150713_162423_diego cannot be reverted.\n";

        return false;
    }
}
