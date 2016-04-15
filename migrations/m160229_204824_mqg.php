<?php
/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

use yii\db\Migration;

class m160229_204824_mqg extends Migration
{
    public function up()
    {
        $this->execute("
            DROP TABLE IF EXISTS meican_connection_log
            ");
        $this->execute("
            CREATE TABLE `meican_connection_event` ( `id` INT AUTO_INCREMENT PRIMARY KEY )
            ");
        $this->execute("
            ALTER TABLE `meican_connection_event` ADD `conn_id` INT NOT NULL ;
            ");
        $this->execute("
            ALTER TABLE `meican_connection_event` ADD INDEX(`conn_id`);
            ");
        $this->execute("
            ALTER TABLE `meican_connection_event` ADD CONSTRAINT `conn_event` FOREIGN KEY (`conn_id`) 
            REFERENCES `meican_connection`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;
            ");
        $this->execute("
            ALTER TABLE `meican_connection_event` ADD `created_at` DATETIME NOT NULL;
            ");
        $this->execute("
            ALTER TABLE `meican_connection_event` ADD `type` ENUM('CREATED','UPDATED','CANCELLED') NOT NULL ;
            ");
        $this->execute("
            ALTER TABLE `meican_connection_event` ADD `author_id` INT NULL AFTER `type`;
            ");
        $this->execute("
            ALTER TABLE `meican_connection_event` ADD INDEX(`created_at`);
            ");
        $this->execute("
            ALTER TABLE `meican_connection_event` ADD INDEX(`type`);
            ");
        $this->execute("
            ALTER TABLE `meican_connection_event` ADD INDEX(`author_id`);
            ");
        $this->execute("
            ALTER TABLE `meican_connection_event` ADD CONSTRAINT `conn_event_author` FOREIGN KEY (`author_id`) 
            REFERENCES `meican_user`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;
            ");
    }

    public function down()
    {
        echo "m160229_204824_mqg cannot be reverted.\n";

        return false;
    }
}
