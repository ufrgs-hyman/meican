<?php

use yii\db\Schema;
use yii\db\Migration;

class m151111_193746_mqg extends Migration
{
    public function up()
    {
        $this->execute("
            CREATE TABLE IF NOT EXISTS `meican_provider_peering` (
              `src_id` int(11) NOT NULL,
              `dst_id` int(11) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ");
        $this->execute("
            ALTER TABLE `meican_provider_peering`
             ADD PRIMARY KEY (`src_id`,`dst_id`), ADD KEY `peering_dst` (`dst_id`);
            ");
        $this->execute("
            ALTER TABLE `meican_provider_peering`
            ADD CONSTRAINT `peering_dst` FOREIGN KEY (`dst_id`) REFERENCES `meican_provider` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
            ADD CONSTRAINT `peering_src` FOREIGN KEY (`src_id`) REFERENCES `meican_provider` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
            ");
        $this->execute("
            DELETE FROM `meican_topo_change` WHERE 1   
            ");
        $this->execute("
            ALTER TABLE `meican_topo_change` 
            CHANGE `item_type` `item_type` 
            ENUM('DOMAIN','PROVIDER','PEERING','SERVICE','NETWORK','DEVICE','BIPORT','UNIPORT','LINK') 
            CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
            ");

        $this->execute("
            DELETE FROM `meican_topo_synchronizer` WHERE 1
            ");

        $this->execute("
            ALTER TABLE `meican_topo_synchronizer` ADD `protocol` ENUM('HTTP','NSI_DS_1_0') NOT NULL AFTER `name`;
            ");

        $this->execute("
            ALTER TABLE `meican_topo_synchronizer` CHANGE `type` `type` ENUM('NSI_TD_2_0_NSAD_1_0','NMWG_TD_3_0') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
            "); 

        $this->execute("
            DELETE FROM `meican_service` WHERE 1   
            ");

        $this->execute("
            ALTER TABLE `meican_service` CHANGE `type` `type` ENUM('NSI_CSP_2_0','NSI_DS_1_0','NSI_TD_2_0','NMWG_TD_3_0','PERFSONAR_TS_1_0') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;    
            ");
    }

    public function down()
    {
        echo "m151111_193746_mqg cannot be reverted.\n";

        return false;
    }

}
