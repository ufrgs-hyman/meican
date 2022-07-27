<?php

use yii\db\Migration;

class m220727_003909_lcl extends Migration
{
    public function up()
    {

        $this->execute("
        
            SET FOREIGN_KEY_CHECKS = 0;
        ");

        $this->execute("
            CREATE TABLE IF NOT EXISTS `meican_device_type` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `name` varchar(100) NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ");


        
        $this->execute("
        
            ALTER TABLE `meican_location`
            ADD COLUMN `device_id` int(11) DEFAULT 1 NOT NULL;
            
        ");

        $this->execute("
        
                ALTER TABLE `meican_location` ADD CONSTRAINT `location_device_id`
                FOREIGN KEY (`device_id`) REFERENCES `meican_device_type`(`id`);
        
        ");


        $this->execute("
        
            SET FOREIGN_KEY_CHECKS = 1;
        ");

    }

    public function down()
    {
        echo "m220727_003909_lcl cannot be reverted.\n";

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
