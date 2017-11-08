<?php

use yii\db\Migration;

class m171107_141314_mqg extends Migration
{
    public function up()
    {
        $this->execute("ALTER TABLE meican_port DROP FOREIGN KEY device_port;");
        $this->execute("ALTER TABLE `meican_port` DROP `device_id`;");
        $this->execute("DROP TABLE meican_device;");
        $this->execute("ALTER TABLE `meican_port` ADD `lat` FLOAT NULL AFTER `name`;");
        $this->execute("ALTER TABLE `meican_port` ADD `lng` FLOAT NULL AFTER `lat`;");
    }

    public function down()
    {
        echo "m171107_141314_mqg cannot be reverted.\n";
        return false;
    }
}
