<?php

use yii\db\Schema;
use yii\db\Migration;

class m160126_144829_diego extends Migration
{
    public function up()
    {
    	$this->execute("
            ALTER TABLE `meican_group` ADD `domain` VARCHAR(60) CHARACTER SET utf8 COLLATE utf8_bin NULL;
        ");
    	 
    	$this->execute("
            ALTER TABLE `meican_group` ADD INDEX(`domain`);
        ");
    	 
    	$this->execute("
            ALTER TABLE `meican_group` ADD CONSTRAINT `group_domain` FOREIGN KEY (`domain`) REFERENCES `meican2`.`meican_domain`(`name`) ON DELETE CASCADE ON UPDATE CASCADE;
        ");
    }

    public function down()
    {
        echo "m160126_144829_diego cannot be reverted.\n";

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
