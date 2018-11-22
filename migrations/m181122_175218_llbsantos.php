<?php

use yii\db\Migration;

class m181122_175218_llbsantos extends Migration
{
    public function up()
    {
        $this->execute("
                ALTER TABLE `meican_user_domain` DROP FOREIGN KEY `user_domain_domain`;");
    }

    public function down()
    {
        echo "m181122_175218_llbsantos cannot be reverted.\n";

        return false;
    }

}
