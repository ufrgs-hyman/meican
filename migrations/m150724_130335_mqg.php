<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

use yii\db\Schema;
use yii\db\Migration;

class m150724_130335_mqg extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `meican_reservation` CHANGE `request_user_id` `request_user_id` INT(11) NULL;
        ");
        $this->execute("
            ALTER TABLE `meican_reservation` DROP FOREIGN KEY `reservation_requester`; 
            ALTER TABLE `meican_reservation` 
            ADD CONSTRAINT `reservation_requester` FOREIGN KEY (`request_user_id`) 
            REFERENCES `meican_user`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;
        ");
    }

    public function down()
    {
        echo "m150724_130335_mqg cannot be reverted.\n";

        return false;
    }
}
