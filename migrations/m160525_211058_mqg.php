<?php
/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

use yii\db\Migration;

class m160525_211058_mqg extends Migration
{
    public function up()
    {
        $this->execute("
            DROP TABLE meican_reservation_recurrence;
            ");
    }

    public function down()
    {
        echo "m160525_211058_mqg cannot be reverted.\n";

        return false;
    }
}
