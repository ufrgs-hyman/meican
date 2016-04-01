<?php
/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

use yii\db\Migration;

class m160331_220240_mqg extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `meican_reservation` CHANGE `start` `start` DATETIME NULL, CHANGE `finish` `finish` DATETIME NULL;
            ");
        $this->execute("
            ALTER TABLE `meican_connection` ADD `version` INT NULL AFTER `gri`;
            ");
        $this->execute("
            UPDATE `meican_connection` SET `version`=1 WHERE 1;
            ");
        $this->execute("
            ALTER TABLE `meican_connection` CHANGE `version` `version` INT(11) NOT NULL;
            ");
        $this->execute("
            ALTER TABLE `meican_connection` ADD `bandwidth` INT NULL AFTER `version`;
            ");
        $this->execute("
            UPDATE `meican_connection` SET `bandwidth`=1 WHERE 1;
            ");
        $this->execute("
            ALTER TABLE `meican_connection_event` ADD `message` TEXT NULL AFTER `type`;
            ");
        $this->execute("
            DELETE FROM `meican_connection_event` WHERE 1;
            ");
        $this->execute("
            ALTER TABLE `meican_connection_event` CHANGE `type` `type` ENUM('USER_CREATE','USER_UPDATE','USER_CANCEL',
            'NSI_RESERVE','NSI_RESERVE_CONFIRMED','NSI_RESERVE_FAILED','NSI_COMMIT','NSI_COMMIT_CONFIRMED',
            'NSI_COMMIT_FAILED','NSI_PROVISION','NSI_PROVISION_CONFIRMED','NSI_TERMINATE','NSI_TERMINATE_CONFIRMED',
            'NSI_SUMMARY','NSI_SUMMARY_CONFIRMED','NSI_DATAPLANE_CHANGE','NSI_RESERVE_RESPONSE',
            'NSI_RESERVE_TIMEOUT') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
            ");
    }

    public function down()
    {
        echo "m160331_220240_mqg cannot be reverted.\n";

        return false;
    }
}
