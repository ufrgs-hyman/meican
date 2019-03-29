<?php

use yii\db\Migration;

class m190329_172000_add_location_name extends Migration
{
    public function up()
    {
        $this->execute("ALTER TABLE `meican_port` ADD `location_name` VARCHAR(100) NULL DEFAULT NULL AFTER `name`;");
    }

    public function down()
    {
        echo "m190329_172000_add_location_name cannot be reverted.\n";

        return false;
    }
}
