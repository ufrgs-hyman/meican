<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

use yii\db\Schema;
use yii\db\Migration;

class m150723_170016_mqg extends Migration
{
    public function up()
    {
        $this->execute("
            DELETE FROM `meican_reservation` WHERE 1
        ");
        $this->execute("
            ALTER TABLE `meican_connection_path` DROP `dst_urn`;
        ");
        $this->execute("
            ALTER TABLE `meican_connection_path` DROP `dst_vlan`;
        ");
        $this->execute("
            ALTER TABLE `meican_connection_path` CHANGE `src_urn` `port_urn` VARCHAR(150) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
        ");
        $this->execute("
            ALTER TABLE `meican_connection_path` CHANGE `src_vlan` `vlan` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
        ");
    }

    public function down()
    {
        echo "m150723_170016_mqg cannot be reverted.\n";

        return false;
    }
    
}
