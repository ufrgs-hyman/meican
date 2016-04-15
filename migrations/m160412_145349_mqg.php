<?php
/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

use yii\db\Migration;

class m160412_145349_mqg extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE  `meican_connection_event` ADD  `data` TEXT NULL AFTER  `type`;
            ");
    }

    public function down()
    {
        echo "m160412_145349_mqg cannot be reverted.\n";

        return false;
    }
}
