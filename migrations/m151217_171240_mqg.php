<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

use yii\db\Schema;
use yii\db\Migration;

class m151217_171240_mqg extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE  `meican_user_settings` CHANGE  `date_format`  `date_format` VARCHAR( 20 ) 
            CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ;
            ");
    }

    public function down()
    {
        echo "m151217_171240_mqg cannot be reverted.\n";

        return false;
    }
}
