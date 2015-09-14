<?php

use yii\db\Schema;
use yii\db\Migration;

class m150707_203004_mqg extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER DATABASE DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;");
        $this->execute("
            CREATE TABLE IF NOT EXISTS `meican_connection_log` (
            `id` int(11) NOT NULL,
              `conn_id` int(11) NOT NULL,
              `date` datetime NOT NULL,
              `received` tinyint(1) NOT NULL,
              `message` text NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ");
        $this->execute("
            ALTER TABLE `meican_connection_log`
            ADD PRIMARY KEY (`id`), ADD KEY `conn_id` (`conn_id`);
            ");
        $this->execute("
            ALTER TABLE `meican_connection_log`
            MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
            ");
        $this->execute("
            ALTER TABLE `meican_connection_log`
            ADD CONSTRAINT `conn_log` FOREIGN KEY (`conn_id`) REFERENCES `meican_connection` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
            ");
        
    }

    public function down()
    {
        echo "m150707_203004_mqg cannot be reverted.\n";

        return false;
    }
}
