<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

use yii\db\Schema;
use yii\db\Migration;

class m151223_140008_mqg extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `meican_user` ADD `email` VARCHAR(60) NOT NULL AFTER `authkey`;
            ");
        $this->execute("
            ALTER TABLE `meican_user` ADD `name` VARCHAR(100) NOT NULL AFTER `email`;
            ");
        $this->execute("
            ALTER TABLE `meican_user` ADD `language` ENUM('en-US','pt-BR') NOT NULL AFTER `name`;
            ");
        $this->execute("
            ALTER TABLE `meican_user` ADD `time_zone` VARCHAR(40) NOT NULL AFTER `language`;
            ");
        $this->execute("
            ALTER TABLE `meican_user` ADD `date_format` VARCHAR(20) NOT NULL AFTER `language`;
            ");
        $this->execute("
            ALTER TABLE `meican_user` ADD `time_format` VARCHAR(10) NOT NULL AFTER `date_format`;
            ");
        $this->execute("
            UPDATE `meican_user` SET `email`=(SELECT `email` FROM `meican_user_settings` WHERE `id`=`meican_user`.`id`) WHERE 1
            ");
        $this->execute("
            UPDATE `meican_user` SET `name`=(SELECT `name` FROM `meican_user_settings` WHERE `id`=`meican_user`.`id`) WHERE 1
            ");
        $this->execute("
            UPDATE `meican_user` SET `date_format`=(SELECT `date_format` FROM `meican_user_settings` WHERE `id`=`meican_user`.`id`) WHERE 1
            ");
        $this->execute("
            UPDATE `meican_user` SET `language`=(SELECT `language` FROM `meican_user_settings` WHERE `id`=`meican_user`.`id`) WHERE 1
            ");
        $this->execute("
            UPDATE `meican_user_settings` SET `time_zone`='UTC' WHERE `time_zone` IS NULL;
            ");
        $this->execute("
            UPDATE `meican_user` SET `time_zone`=(SELECT `time_zone` FROM `meican_user_settings` WHERE `id`=`meican_user`.`id`) WHERE 1
            ");
        $this->execute("
            ALTER TABLE `meican_user_settings` DROP `language`;
            ");
        $this->execute("
            ALTER TABLE `meican_user_settings` DROP `time_zone`;
            ");
        $this->execute("
            ALTER TABLE `meican_user_settings` DROP `date_format`;
            ");
        $this->execute("
            ALTER TABLE `meican_user_settings` DROP `name`;
            ");
        $this->execute("
            ALTER TABLE `meican_user_settings` DROP `email`;
            ");
        $this->execute("
            ALTER TABLE `meican_user` ADD UNIQUE(`email`);
            ");
        $this->execute("
            ALTER TABLE `meican_user` ADD INDEX(`name`);
            ");
        $this->execute("
            UPDATE `meican_user` SET `date_format`='dd/MM/yyyy' WHERE 1
            ");
        $this->execute("
            UPDATE `meican_user` SET `time_format`='HH:mm' WHERE 1
            ");
        $this->execute("
            ALTER TABLE `meican_user` ADD `created_at` DATETIME NULL AFTER `id`;
            ");
    }

    public function down()
    {
        echo "m151223_140008_mqg cannot be reverted.\n";

        return false;
    }
}
