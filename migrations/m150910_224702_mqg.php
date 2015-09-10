<?php

use yii\db\Schema;
use yii\db\Migration;

class m150910_224702_mqg extends Migration
{
    public function up()
    {
        $this->execute("
            DELETE FROM `meican_provider` WHERE 1;
            ");
        $this->execute("
            ALTER TABLE `meican_provider` CHANGE `type` `type` ENUM('AGG','UPA') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;");
    }

    public function down()
    {
        echo "m150910_224702_mqg cannot be reverted.\n";

        return false;
    }
}
