<?php

use yii\db\Schema;
use yii\db\Migration;

class m150904_213353_mqg extends Migration
{
    public function up()
    {
        $this->execute("
            DELETE FROM `meican_reservation` WHERE `type`='TEST'");
        $this->execute("DROP TABLE meican_automated_test");
    }

    public function down()
    {
        echo "m150904_213353_mqg cannot be reverted.\n";

        return false;
    }
}
