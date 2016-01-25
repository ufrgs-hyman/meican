<?php
/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

use yii\db\Schema;
use yii\db\Migration;

class m160124_190745_mqg extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE  `meican_device` CHANGE  `latitude`  `latitude` DECIMAL( 9, 6 ) NULL DEFAULT NULL ;
            ");
        $this->execute("
            ALTER TABLE  `meican_device` CHANGE  `longitude`  `longitude` DECIMAL( 9, 6 ) NULL DEFAULT NULL ;
            ");
    }

    public function down()
    {
        echo "m160124_190745_mqg cannot be reverted.\n";

        return false;
    }
}
