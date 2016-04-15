<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

use yii\db\Schema;
use yii\db\Migration;

class m151216_165811_mqg extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `meican_connection` DROP INDEX `external_id`;
            ");
        $this->execute("
            ALTER TABLE `meican_connection` ADD `gri` VARCHAR(65) NULL AFTER `external_id`;
            ");
        $this->execute("
            ALTER TABLE `meican_connection` ADD INDEX(`external_id`);
            ");
        $this->execute("
            ALTER TABLE `meican_connection` ADD INDEX(`gri`);
            ");
        $this->execute("
            ALTER TABLE `meican_connection` ADD INDEX(`status`);
            ");
        $this->execute("
            ALTER TABLE `meican_connection` ADD INDEX(`auth_status`);
            ");
        $this->execute("
            ALTER TABLE `meican_connection` ADD INDEX(`dataplane_status`);
            ");
        $this->execute("
            ALTER TABLE `meican_connection` ADD INDEX(`start`);
            ");
        $this->execute("
            ALTER TABLE `meican_connection` ADD INDEX(`finish`);
            ");
        $this->execute("
            ALTER TABLE `meican_reservation` ADD `gri` VARCHAR(65) NULL AFTER `id`;
            ");
        $this->execute("
            ALTER TABLE `meican_reservation` ADD INDEX(`gri`);
            ");
        $this->execute("
            ALTER TABLE `meican_reservation` ADD INDEX(`date`);
            ");
        $this->execute("
            ALTER TABLE `meican_reservation` ADD INDEX(`bandwidth`);
            ");
        $this->execute("
            ALTER TABLE `meican_reservation` ADD INDEX(`start`);
            ");
        $this->execute("
            ALTER TABLE `meican_reservation` ADD INDEX(`finish`);
            ");
        $this->execute("
            ALTER TABLE `meican_reservation` ADD INDEX(`requester_nsa`);
            ");
        $this->execute("
            ALTER TABLE `meican_reservation` ADD INDEX(`provider_nsa`);
            ");
        $this->execute("
            ALTER TABLE `meican_reservation` ADD INDEX(`name`);
            ");
        $this->execute("
            ALTER TABLE `meican_reservation` ADD `protected` TINYINT NULL AFTER `bandwidth`;
            ");
        $this->execute("
            UPDATE `meican_reservation` SET `protected`=0 WHERE 1
            ");
        $this->execute("
            ALTER TABLE `meican_reservation` CHANGE `protected` `protected` TINYINT(1) NOT NULL;
            ");
        $this->execute("
            ALTER TABLE `meican_connection` ADD `protected` TINYINT NULL AFTER `finish`;
            ");
        $this->execute("
            UPDATE `meican_connection` SET `protected`=0 WHERE 1
            ");
        $this->execute("
            ALTER TABLE `meican_connection` CHANGE `protected` `protected` TINYINT(1) NOT NULL;
            ");
    }

    public function down()
    {
        echo "m151216_165811_mqg cannot be reverted.\n";

        return false;
    }
}
