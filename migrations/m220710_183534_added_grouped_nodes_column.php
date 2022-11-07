<?php

use yii\db\Migration;

class m220710_183534_added_grouped_nodes_column extends Migration
{
    public function up()
    {
        $this->execute(
            "ALTER TABLE `meican_domain` 
            ADD COLUMN `grouped_nodes` boolean NULL DEFAULT TRUE"
        );

    }

    public function down()
    {
        echo "m220710_183534_added_grouped_nodes_column cannot be reverted.\n";

        return false;
    }
}
