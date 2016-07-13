<?php
/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

use yii\db\Migration;

class m160629_191436_mqg extends Migration
{
    public function up()
    {
        $this->execute("
           ALTER TABLE `meican_sche_task` DROP `status`;");
        $this->execute("
           ALTER TABLE meican_sche_task DROP INDEX task_id;");
        $this->execute("
           ALTER TABLE `meican_sche_task` CHANGE `obj_data` `obj_data` TEXT NOT NULL;");
    }

    public function down()
    {
        echo "m160629_191436_mqg cannot be reverted.\n";

        return false;
    }
}
