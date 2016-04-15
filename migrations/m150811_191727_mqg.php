<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

use yii\db\Schema;
use yii\db\Migration;

class m150811_191727_mqg extends Migration
{
    public function up()
    {
        $this->execute("
CREATE TABLE IF NOT EXISTS `meican_cron` (
`id` int(11) NOT NULL,
  `external_id` varchar(30) DEFAULT NULL,
  `task_type` enum('SYNC','TEST') NOT NULL,
  `task_id` int(11) NOT NULL,
  `status` enum('ENABLED','DISABLED','DELETED','PROCESSING') NOT NULL,
  `freq` varchar(30) NOT NULL,
  `last_run_at` datetime DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
        ");
        $this->execute("
ALTER TABLE `meican_cron`
 ADD PRIMARY KEY (`id`), ADD KEY `task_type` (`task_type`), ADD KEY `status` (`status`), ADD KEY `external_id` (`external_id`), 
 ADD KEY `task_id` (`task_id`);
        ");
        $this->execute("
ALTER TABLE `meican_cron`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
        ");
        $this->execute("
ALTER TABLE `meican_topo_synchronizer` DROP `sync_date`;
        ");
        $this->execute("
ALTER TABLE `meican_topo_synchronizer` CHANGE `enabled` `subscribed` TINYINT(1) NOT NULL;
        ");
        $this->execute("
ALTER TABLE `meican_topo_synchronizer` ADD `provider_nsa` VARCHAR(200) NULL AFTER `auto_apply`;
        ");
        $this->execute("
ALTER TABLE `meican_connection_log` CHANGE `date` `logged_at` DATETIME NOT NULL;
        ");
        $this->execute("
ALTER TABLE `meican_topo_synchronizer` DROP INDEX `url`;
        ");
        $this->execute("
ALTER TABLE `meican_topo_synchronizer` ADD UNIQUE(`provider_nsa`);
        ");
    }

    public function down()
    {
        echo "m150811_191727_mqg cannot be reverted.\n";

        return false;
    }
}
