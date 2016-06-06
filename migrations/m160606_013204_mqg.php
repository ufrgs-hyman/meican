<?php
/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

use yii\db\Migration;

class m160606_013204_mqg extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE  `meican_port` ADD  `capacity` INT NULL AFTER  `name` ;
            ");
    }

    public function down()
    {
        echo "m160606_013204_mqg cannot be reverted.\n";

        return false;
    }
}
