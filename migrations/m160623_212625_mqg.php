<?php
/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

use yii\db\Migration;

class m160623_212625_mqg extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `meican_connection` ADD `resources_status` ENUM('PROVISIONED','RELEASED') NOT NULL AFTER `status`;
            ");
    }

    public function down()
    {
        echo "m160623_212625_mqg cannot be reverted.\n";

        return false;
    }
}
