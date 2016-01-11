<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
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
            RENAME TO meican_task;
            ");
    }

    public function down()
    {
        echo "m160111_151213_mqg cannot be reverted.\n";

        return false;
    }
}
