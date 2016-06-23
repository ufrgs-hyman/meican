<?php
/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

use yii\db\Migration;

class m160623_180829_mqg extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `meican_connection` CHANGE `status` `status` ENUM('PENDING','CREATED','CONFIRMED','SUBMITTED',
                'PROVISIONED','CANCEL REQUESTED','CANCELLED','FAILED ON CREATE','FAILED ON CONFIRM','FAILED ON SUBMIT',
                'FAILED ON PROVISION','WAITING_DATAPLANE','RELEASING','RELEASED') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
            ");
        $this->execute("
            ALTER TABLE `meican_connection_event` ADD `status` ENUM('INPROGRESS','FINISHED') NULL AFTER `type`;
            ");
        $this->execute("
            UPDATE `meican_connection_event` SET `status`='FINISHED' WHERE 1;
            ");
        $this->execute("
            ALTER TABLE `meican_connection_event` CHANGE `type` `type` ENUM('USER_CREATE','USER_UPDATE','USER_CANCEL','NSI_RESERVE',
            'NSI_RESERVE_CONFIRMED','NSI_RESERVE_FAILED','NSI_COMMIT','NSI_COMMIT_CONFIRMED','NSI_COMMIT_FAILED','NSI_PROVISION',
            'NSI_PROVISION_CONFIRMED','NSI_TERMINATE','NSI_TERMINATE_CONFIRMED','NSI_SUMMARY','NSI_SUMMARY_CONFIRMED','NSI_DATAPLANE_CHANGE',
            'NSI_RESERVE_RESPONSE','NSI_RESERVE_TIMEOUT','NSI_RELEASE','NSI_RELEASE_CONFIRMED','NSI_MESSAGE_TIMEOUT','NSI_ABORT',
            'NSI_ABORT_CONFIRMED') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
            ");
    }

    public function down()
    {
        echo "m160623_180829_mqg cannot be reverted.\n";

        return false;
    }
}
