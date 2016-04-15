<?php
/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

use yii\db\Schema;
use yii\db\Migration;

class m160115_172100_mqg extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `meican_topo_sync_event` ADD INDEX(`started_at`);
            ");
    }

    public function down()
    {
        echo "m160115_172100_mqg cannot be reverted.\n";

        return false;
    }
}
