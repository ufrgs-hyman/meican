<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

use yii\db\Schema;
use yii\db\Migration;

class m150813_170902_mqg extends Migration
{
    public function up()
    {
         $this->execute("
ALTER TABLE `meican_topo_change` CHANGE `sync_id` `sync_event_id` INT(11) NULL DEFAULT NULL;
        ");
         $this->execute("
CREATE TABLE IF NOT EXISTS `meican_topo_sync_event` (
`id` int(11) NOT NULL,
  `started_at` datetime NOT NULL,
  `status` enum('SUCCESS','FAILED','INPROGRESS') NOT NULL,
  `progress` int(11) NOT NULL,
  `sync_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");
         $this->execute("
ALTER TABLE `meican_topo_sync_event`
 ADD PRIMARY KEY (`id`), ADD KEY `sync_id` (`sync_id`);
        ");
         $this->execute("
ALTER TABLE `meican_topo_sync_event`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
         $this->execute("
ALTER TABLE `meican_topo_sync_event`
ADD CONSTRAINT `sync_event` FOREIGN KEY (`sync_id`) REFERENCES `meican_topo_synchronizer` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;");
         $this->execute("
DELETE FROM `meican_topo_change` WHERE 1
        ");
         $this->execute("
ALTER TABLE `meican_topo_change` ADD CONSTRAINT `sync_change` FOREIGN KEY (`sync_event_id`) 
REFERENCES `meican_topo_sync_event`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;
        ");
    }

    public function down()
    {
        echo "m150813_170902_mqg cannot be reverted.\n";

        return false;
    }
}
