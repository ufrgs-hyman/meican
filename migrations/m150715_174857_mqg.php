<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

use yii\db\Schema;
use yii\db\Migration;

class m150715_174857_mqg extends Migration
{
    public function up()
    {
        $this->execute("
ALTER TABLE `meican_port` ADD INDEX(`directionality`);");
        $this->execute("
ALTER TABLE `meican_port` ADD INDEX(`type`);");
        $this->execute("
INSERT INTO `meican_preference` (`name`, `value`) VALUES ('circuits.default.cs.url', NULL);
        ");
        $this->execute("
ALTER TABLE `meican_device` CHANGE `node` `node` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL;");
        
    }

    public function down()
    {
        echo "m150715_174857_mqg cannot be reverted.\n";

        return false;
    }
}
