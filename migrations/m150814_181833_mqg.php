<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

use yii\db\Schema;
use yii\db\Migration;

class m150814_181833_mqg extends Migration
{
    public function up()
    {
        
        $this->execute("
DELETE FROM `meican_provider` WHERE 1
        ");
         $this->execute("
ALTER TABLE `meican_provider` DROP FOREIGN KEY `prov_domain`;
        ");
         $this->execute("
ALTER TABLE `meican_provider` CHANGE `domain_id` `domain_id` INT(11) NOT NULL;
        ");
         $this->execute("
ALTER TABLE `meican_provider` ADD CONSTRAINT `domain_provider` FOREIGN KEY (`domain_id`) 
REFERENCES `meican_domain`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;
        ");
         $this->execute("
ALTER TABLE `meican_topo_synchronizer` CHANGE `subscribed` `subscription_id` VARCHAR(100) NULL;
        ");
    }

    public function down()
    {
        echo "m150814_181833_mqg cannot be reverted.\n";

        return false;
    }
}
