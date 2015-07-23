<?php

use yii\db\Schema;
use yii\db\Migration;

class m150723_202058_mqg extends Migration
{
    public function up()
    {
        $this->execute("
            INSERT INTO `meican_preference` (`name`, `value`) VALUES ('circuits.meican.requester.url', NULL);
        ");
    }

    public function down()
    {
        echo "m150723_202058_mqg cannot be reverted.\n";

        return false;
    }
}
