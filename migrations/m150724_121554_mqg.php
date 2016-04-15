<?php

use yii\db\Schema;
use yii\db\Migration;

class m150724_121554_mqg extends Migration
{
    public function up()
    {
        $this->execute("
            INSERT INTO `meican_preference` (`name`, `value`) VALUES ('circuits.protocol', 'nsi.cs.2.0');
        ");
    }

    public function down()
    {
        echo "m150724_121554_mqg cannot be reverted.\n";

        return false;
    }
}
