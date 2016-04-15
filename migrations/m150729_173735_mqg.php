<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

use yii\db\Schema;
use yii\db\Migration;

class m150729_173735_mqg extends Migration
{
    public function up()
    {
        $this->execute("
            CREATE TABLE IF NOT EXISTS `meican_topo_synchronizer` (
            `id` int(11) NOT NULL,
              `name` varchar(200) NOT NULL,
              `type` enum('NSI_DS_1_0','NSI_TD_2_0','NMWG_TD_1_0','PERFSONAR_TS_1_0') NOT NULL,
              `auto_apply` tinyint(1) NOT NULL,
              `url` varchar(250) NOT NULL,
              `enabled` tinyint(1) NOT NULL,
              `sync_date` datetime DEFAULT NULL
            ) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
        ");
        $this->execute("
            ALTER TABLE `meican_topo_synchronizer`
            ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `url` (`url`);
            ALTER TABLE `meican_topo_synchronizer`
            MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
        ");
        $this->execute("
            CREATE TABLE IF NOT EXISTS `meican_topo_change` (
            `id` int(11) NOT NULL,
              `sync_id` int(11) DEFAULT NULL,
              `domain` varchar(100) NOT NULL,
              `status` enum('PENDING','APPLIED','FAILED') NOT NULL,
              `type` enum('CREATE','UPDATE','DELETE') NOT NULL,
              `item_type` enum('DOMAIN','PROVIDER','SERVICE','NETWORK','DEVICE','BIPORT','UNIPORT','LINK') NOT NULL,
              `item_id` int(11) DEFAULT NULL,
              `data` text NOT NULL,
              `applied_at` datetime DEFAULT NULL,
              `error` text
            ) ENGINE=InnoDB AUTO_INCREMENT=14403 DEFAULT CHARSET=utf8;
        ");
        $this->execute("
            ALTER TABLE `meican_topo_change`
            ADD PRIMARY KEY (`id`), ADD KEY `sync` (`sync_id`), ADD KEY `domain` (`domain`), 
            ADD KEY `type` (`type`), ADD KEY `item_type` (`item_type`), ADD KEY `status` (`status`), 
            ADD KEY `applied_at` (`applied_at`), ADD KEY `applied_at_2` (`applied_at`);
        ");
        $this->execute("
            ALTER TABLE `meican_topo_change`
            MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
        ");
    }

    public function down()
    {
        echo "m150729_173735_mqg cannot be reverted.\n";

        return false;
    }
}
