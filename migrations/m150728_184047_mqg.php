<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

use yii\db\Schema;
use yii\db\Migration;

class m150728_184047_mqg extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `meican_provider` ADD `domain_id` INT NULL AFTER `longitude`;
            ALTER TABLE `meican_provider` ADD CONSTRAINT `prov_domain` 
            FOREIGN KEY (`domain_id`) REFERENCES `meican_domain`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;
        ");
        $this->execute("
            ALTER TABLE `meican_port` ADD `vlan_range` TEXT NULL AFTER `granularity`;
            ALTER TABLE `meican_vlan_range` DROP FOREIGN KEY `port_vlan`;
            DROP TABLE meican_vlan_range;
        ");
        $this->execute("
            DELETE FROM `meican_provider` WHERE 1;
        ");
        $this->execute("
            ALTER TABLE `meican_service` CHANGE `type` `type` 
            ENUM('NSI_CSP_2_0','NSI_DS_1_0','NSI_TD_2_0','NMWG_TD_1_0','PERFSONAR_TS_1_0') 
            CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
        ");
    }

    public function down()
    {
        echo "m150728_184047_mqg cannot be reverted.\n";

        return false;
    }
}
