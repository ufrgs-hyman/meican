<?php

use yii\db\Migration;

class m171117_021844_mqg extends Migration
{
    public function up()
    {
        $this->execute("DELETE FROM `meican_network` WHERE 1");
        $this->execute("ALTER TABLE `meican_network` ADD `version` DATETIME AFTER `urn`;");
    }

    public function down()
    {
        echo "m171117_021844_mqg cannot be reverted.\n";

        return false;
    }
}
