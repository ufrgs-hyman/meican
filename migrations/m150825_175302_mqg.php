<?php

use yii\db\Schema;
use yii\db\Migration;

class m150825_175302_mqg extends Migration
{
    public function up()
    {
        $this->execute("
            UPDATE `meican_preference` SET `value` = 'dummy' WHERE `meican_preference`.`name` = 'circuits.default.provider.nsa';
        ");
        $this->execute("
            UPDATE `meican_preference` SET `value` = 'dummy' WHERE `meican_preference`.`name` = 'circuits.default.cs.url';
        ");
        $this->execute("
            UPDATE `meican_preference` SET `value` = 'dummy' WHERE `meican_preference`.`name` = 'meican.nsa';
        ");
    }

    public function down()
    {
        echo "m150825_175302_mqg cannot be reverted.\n";

        return false;
    }
}
