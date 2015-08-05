<?php

use yii\db\Schema;
use yii\db\Migration;

class m150729_192749_mqg extends Migration
{
    public function up()
    {
        $this->execute("
            UPDATE `meican_preference` SET `value` = 'NSI_CSP_2_0' WHERE `meican_preference`.`name` = 'circuits.protocol';
        ");
    }

    public function down()
    {
        echo "m150729_192749_mqg cannot be reverted.\n";

        return false;
    }
}
