<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

use yii\db\Schema;
use yii\db\Migration;

/**
 * @author Maurício Quatrin Guerreiro @mqgmaster
 */
class m160111_151213_mqg extends Migration
{
    public function up()
    {
        $this->execute("
            DELETE FROM `meican_cron` WHERE 1;
            ");
        $this->execute("
            ALTER TABLE meican_cron
            RENAME TO meican_sche_task;
            ");
        $this->execute("
            ALTER TABLE `meican_sche_task` CHANGE `task_type` `obj_class` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
        ");
        $this->execute("
            ALTER TABLE `meican_sche_task` CHANGE `task_id` `obj_data` INT(11) NOT NULL;
        ");
        $this->execute("
            ALTER TABLE `meican_sche_task` DROP `external_id`;
            ");
        $this->execute("
            ALTER TABLE  `meican_sche_task` CHANGE  `status`  `status` ENUM(  'ENABLED',  'DISABLED',  'PROCESSING' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
            ");
    }

    public function down()
    {
        echo "m160111_151213_mqg cannot be reverted.\n";

        return false;
    }
}
