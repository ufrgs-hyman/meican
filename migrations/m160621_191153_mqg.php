<?php
/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

use yii\db\Migration;

class m160621_191153_mqg extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `meican_reservation` DROP `gri`;
            ");
        $this->execute("
            ALTER TABLE `meican_reservation` DROP `protected`;
            ");
        $this->execute("
            ALTER TABLE `meican_connection` CHANGE `reservation_id` `reservation_id` INT(11) NULL;
            ");
        $this->execute("
            ALTER TABLE `meican_connection` ADD `parent_id` INT NULL AFTER `finish`;
            ");
        $this->execute("
            ALTER TABLE `meican_connection` ADD INDEX(`parent_id`);
            ");
        $this->execute("
            ALTER TABLE `meican_connection` ADD CONSTRAINT `parent_connection` FOREIGN KEY (`parent_id`)
            REFERENCES `meican_connection`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;
            ");
        $this->execute("
            ALTER TABLE `meican_connection` ADD `name` VARCHAR(100) NULL AFTER `id`;
            ");
    }

    public function down()
    {
        echo "m160621_191153_mqg cannot be reverted.\n";

        return false;
    }
}
