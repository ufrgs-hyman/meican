<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

use yii\db\Schema;
use yii\db\Migration;

class m150713_220322_mqg extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `meican_service` 
            CHANGE `type` `type` 
            ENUM('NSI_CONNECTION','NSI_DISCOVERY','NSI_TOPOLOGY','NMWG_TOPOLOGY','NMWG_TOPO_PERFSONAR') 
            CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
        ");
    }

    public function down()
    {
        echo "m150713_220322_mqg cannot be reverted.\n";

        return false;
    }
}
