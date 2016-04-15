<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

use yii\db\Schema;
use yii\db\Migration;

class m150827_152944_diego extends Migration
{
    public function up()
    {
    	$this->execute("
			ALTER TABLE `meican_bpm_node` CHANGE `type` `type` ENUM('New_Request','Duration','Domain','User','Bandwidth','Request_User_Authorization','Request_Group_Authorization','Accept_Automatically','Deny_Automatically','Hour','WeekDay','Group') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;	
        ");
    	
    	$this->execute("
			ALTER TABLE `meican_bpm_flow_control` CHANGE `type` `type` ENUM('New_Request','Duration','Domain','User','Bandwidth','Request_User_Authorization','Request_Group_Authorization','Accept_Automatically','Deny_Automatically','Hour','WeekDay','Group') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
        ");
    	
   	}

    public function down()
    {
        echo "m150827_152944_diego cannot be reverted.\n";

        return false;
    }  
}
