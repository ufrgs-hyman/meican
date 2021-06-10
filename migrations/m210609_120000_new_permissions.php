<?php
/**
 * @copyright Copyright (c) 2012-2021 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

use yii\db\Schema;
use yii\db\Migration;

class m210609_120000_new_permissions extends Migration
{
    public function up()
    {
  
		$this->execute("
			INSERT INTO `meican_auth_item` (`name`, `type`, `description`, `rule_name`, `data`, `created_at`, `updated_at`) VALUES
				('g10', 1, NULL, NULL, NULL, 1623250718, 1623250718),
				('readAuthorization', 2, NULL, NULL, NULL, 1623250718, 1623250718),
				('updateAuthorization', 2, NULL, NULL, NULL, 1623250718, 1623250718);
			");
		
			
		$this->execute("
			INSERT INTO `meican_group` (`name`, `role_name`, `type`) VALUES
				('Authorization Manager', 'g10', 'DOMAIN');
			");
			
		
		$this->execute("
			INSERT INTO `meican_auth_item_child` (`parent`, `child`) VALUES
				('g2', 'readAuthorization'),
				('g2', 'updateAuthorization'),
				('g5', 'readAuthorization'),
				('g5', 'updateAuthorization'),
				('g10', 'readAuthorization'),
				('g10', 'updateAuthorization');
			");
		
    }
    
    public function down()
    {
        echo "m210609_120000_new_permissions cannot be reverted.\n";

        return false;
    }
    
 }
