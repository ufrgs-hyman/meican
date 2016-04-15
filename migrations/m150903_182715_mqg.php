<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

use yii\db\Schema;
use yii\db\Migration;

class m150903_182715_mqg extends Migration
{
    public function up()
    {
        $this->execute("
            INSERT INTO `meican_preference` (`name`, `value`) VALUES ('aaa.federation.group', NULL), ('aaa.federation.domain', NULL);
            INSERT INTO `meican_preference` (`name`, `value`) VALUES ('aaa.federation.enabled', 'false');");
    }

    public function down()
    {
        echo "m150903_182715_mqg cannot be reverted.\n";

        return false;
    }
}
