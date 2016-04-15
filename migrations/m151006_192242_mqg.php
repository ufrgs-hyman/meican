<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

use yii\db\Schema;
use yii\db\Migration;

class m151006_192242_mqg extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `meican_user_settings` ADD `time_zone` VARCHAR(40) NULL AFTER `language`;
            ");
        $this->execute("
            ALTER TABLE `meican_user_settings` ADD `topo_viewer` VARCHAR(40) NULL AFTER `email`;
            ");
    }

    public function down()
    {
        echo "m151006_192242_mqg cannot be reverted.\n";

        return false;
    }
}
