<?php

use yii\db\Schema;
use yii\db\Migration;

class m150923_211141_mqg extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `meican_domain` ADD `graph_x` INT NULL AFTER `name`;
            ");
        $this->execute("
            ALTER TABLE `meican_domain` ADD `graph_y` INT NULL AFTER `graph_x`;
            ");
        $this->execute("
            ALTER TABLE `meican_domain` ADD `color` VARCHAR(10) NULL AFTER `graph_y`;
            ");
        $this->execute("
            ALTER TABLE `meican_device` ADD `graph_x` INT NULL AFTER `longitude`;
            ");
        $this->execute("
            ALTER TABLE `meican_device` ADD `graph_y` INT NULL AFTER `graph_x`;
            ");
    }

    public function down()
    {
        echo "m150923_211141_mqg cannot be reverted.\n";

        return false;
    }
}
