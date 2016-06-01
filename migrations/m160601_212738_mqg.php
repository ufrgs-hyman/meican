<?php
/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

use yii\db\Migration;

class m160601_212738_mqg extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `meican_connection` DROP `gri`;
            ");
        $this->execute("
            ALTER TABLE `meican_connection` ADD `type` ENUM('OSCARS','NSI') NULL AFTER `id`;
            ");
        $this->execute("
            UPDATE `meican_connection` SET `type`='NSI' WHERE 1;
            ");
        $this->execute("
            ALTER TABLE `meican_connection` DROP `protected`;
            ");
    }

    public function down()
    {
        echo "m160601_212738_mqg cannot be reverted.\n";

        return false;
    }
}
