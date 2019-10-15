<?php

use yii\db\Migration;

class m190702_183938_create_location_table extends Migration
{
    public function up()
    {
        $this->execute("
            CREATE TABLE IF NOT EXISTS `meican_location` (
                `id` int(11) NOT NULL,
                `name` varchar(100) NOT NULL,
                `lat` float NOT NULL,
                `lng` float NOT NULL,
                `domain_id` int(11) NOT NULL
            ) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
            ");

        $this->execute("
            ALTER TABLE `meican_location` 
                ADD PRIMARY KEY (`id`);
            ");

        $this->execute("
            ALTER TABLE `meican_location`
                MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
            ");

        $this->execute("
            ALTER TABLE `meican_port` 
                ADD COLUMN `location_id` int(11) NULL DEFAULT NULL;
            ");

        $this->execute("
            ALTER TABLE `meican_port` 
                ADD CONSTRAINT `location_port` FOREIGN KEY (`location_id`) REFERENCES `meican_location` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;
            ");

        $this->execute("
        ALTER TABLE `meican_location` 
            ADD CONSTRAINT `location_domain` FOREIGN KEY (`domain_id`) REFERENCES `meican_domain` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
        ");
    }

    public function down()
    {
        echo "m190702_183938_create_location_table cannot be reverted.\n";

        return false;
    }
}
