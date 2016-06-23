<?php
/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

use yii\db\Migration;

class m160623_214253_mqg extends Migration
{
    public function up()
    {
        $this->execute("
           ALTER TABLE  `meican_connection_event` CHANGE  `status`  `status` ENUM(  'INPROGRESS',  'FINISHED' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ;
            ");
    }

    public function down()
    {
        echo "m160623_214253_mqg cannot be reverted.\n";

        return false;
    }
}
