<?php

use yii\db\Migration;

class m220810_164059_lcl extends Migration
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

            // $this->execute(" 
            // INSERT INTO `meican_device_type`(name) VALUES(`GenericDevice`);
            // ");

            // $this->execute(" 
            // INSERT INTO `meican_device_type`(name) VALUES(`Device1`);
            // ");

            // $this->execute(" 
            // INSERT INTO `meican_device_type`(name) VALUES(`Device2`);
            // ");

        $this->execute("
        ALTER TABLE `meican_port`
        ADD COLUMN `devicetype_id` int(11) DEFAULT 1 NOT NULL;
        ");

        $this->execute("
        ALTER TABLE `meican_port` ADD CONSTRAINT `port_devicetype_id`
        FOREIGN KEY (`devicetype_id`) REFERENCES `meican_device_type`(`id`);
        ");



        $this->execute("
        SET FOREIGN_KEY_CHECKS = 1;
        ");

}

    

    public function down()
    {
        echo "m220810_164059_lcl cannot be reverted.\n";

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
