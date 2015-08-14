<?php

use yii\db\Schema;
use yii\db\Migration;

class m150810_183724_diego extends Migration
{
    public function up()
    {
    	$this->execute("
            ALTER TABLE `meican_group` ADD `type` ENUM('DOMAIN','SYSTEM') NULL ;
        ");
    	
    	$this->execute("
            UPDATE `meican_group` SET `type`='DOMAIN';
        ");
    	
    	$this->execute("
            ALTER TABLE `meican_group` CHANGE `type` `type` ENUM('DOMAIN','SYSTEM') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
        ");
    	
    	$this->execute("
            ALTER TABLE `meican_user_domain` ADD INDEX(`user_id`);
        ");
    	
    	$this->execute("
            ALTER TABLE meican_user_domain DROP INDEX user_id;
        ");

    	$this->execute("
            ALTER TABLE meican_user_domain DROP FOREIGN KEY dom_user_role;
        ");
    	
    	$this->execute("
            ALTER TABLE meican_user_domain DROP COLUMN domain_id;
        ");
    	
    	$this->execute("
            ALTER TABLE `meican_user_domain` ADD `domain` VARCHAR(60) NULL ;
        ");
    	
    	$this->execute("
            ALTER TABLE `meican_bpm_workflow` CHANGE `domain` `domain` VARCHAR(60) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
        ");
    	
    	$this->execute("
			DELETE FROM `meican_group`;
		");
    	
    	$this->execute("
			DELETE FROM `meican_auth_assignment`;
		");
    	
    	$this->execute("
			DELETE FROM `meican_auth_item`;
		");
    	
    	$this->execute("
			DELETE FROM `meican_auth_item_child`;
		");
    	
    	$this->execute("
	    	INSERT INTO `meican_auth_item` (`name`, `type`, `description`, `rule_name`, `data`, `created_at`, `updated_at`) VALUES
	    	('createDomain', 2, NULL, NULL, NULL, 1439482402, 1439482402),
	    	('createDomainTopology', 2, NULL, NULL, NULL, 1439482385, 1439482385),
	    	('createGroup', 2, NULL, NULL, NULL, 1417803786, 1417803786),
	    	('createReservation', 2, NULL, NULL, NULL, 1417799563, 1417799563),
	    	('createRole', 2, NULL, NULL, NULL, 1439221347, 1439221347),
	    	('createSynchronizer', 2, NULL, NULL, NULL, 1439491351, 1439491351),
	    	('createTest', 2, NULL, NULL, NULL, 1439482385, 1439482385),
	    	('createUser', 2, NULL, NULL, NULL, 1417799563, 1417799563),
	    	('createWorkflow', 2, NULL, NULL, NULL, 1431101273, 1431101273),
	    	('deleteDomain', 2, NULL, NULL, NULL, 1439482402, 1439482402),
	    	('deleteDomainTopology', 2, NULL, NULL, NULL, 1439482385, 1439482385),
	    	('deleteGroup', 2, NULL, NULL, NULL, 1417803786, 1417803786),
	    	('deleteReservation', 2, NULL, NULL, NULL, 1417799563, 1417799563),
	    	('deleteRole', 2, NULL, NULL, NULL, 1439230849, 1439230849),
	    	('deleteSynchronizer', 2, NULL, NULL, NULL, 1439486449, 1439486449),
	    	('deleteTest', 2, NULL, NULL, NULL, 1439482385, 1439482385),
	    	('deleteUser', 2, NULL, NULL, NULL, 1417799563, 1417799563),
	    	('deleteWorkflow', 2, NULL, NULL, NULL, 1431101273, 1431101273),
	    	('g1', 1, NULL, NULL, NULL, 1418403081, 1418403081),
	    	('readConfiguration', 2, NULL, NULL, NULL, 1439229046, 1439229046),
	    	('readDomain', 2, NULL, NULL, NULL, 1439482402, 1439482402),
	    	('readDomainTopology', 2, NULL, NULL, NULL, 1439482385, 1439482385),
	    	('readGroup', 2, NULL, NULL, NULL, 1417803786, 1417803786),
	    	('readReservation', 2, NULL, NULL, NULL, 1417802547, 1417802547),
	    	('readRole', 2, NULL, NULL, NULL, 1439230399, 1439230399),
	    	('readSynchronizer', 2, NULL, NULL, NULL, 1439486449, 1439486449),
	    	('readTest', 2, NULL, NULL, NULL, 1439482385, 1439482385),
	    	('readTopology', 2, NULL, NULL, NULL, 1417803786, 1417803786),
	    	('readUser', 2, NULL, NULL, NULL, 1417803786, 1417803786),
	    	('readWorkflow', 2, NULL, NULL, NULL, 1431101273, 1431101273),
	    	('updateAggregator', 2, NULL, NULL, NULL, 1431109580, 1431109580),
	    	('updateConfiguration', 2, NULL, NULL, NULL, 1439230917, 1439230917),
	    	('updateDomain', 2, NULL, NULL, NULL, 1439482402, 1439482402),
	    	('updateDomainTopology', 2, NULL, NULL, NULL, 1439482385, 1439482385),
	    	('updateGroup', 2, NULL, NULL, NULL, 1417803786, 1417803786),
	    	('updateReservation', 2, NULL, NULL, NULL, 1417799563, 1417799563),
	    	('updateRole', 2, NULL, NULL, NULL, 1439230849, 1439230849),
	    	('updateSynchronizer', 2, NULL, NULL, NULL, 1439486449, 1439486449),
	    	('updateTest', 2, NULL, NULL, NULL, 1439482385, 1439482385),
	    	('updateTopology', 2, NULL, NULL, NULL, 1417799563, 1417799563),
	    	('updateUser', 2, NULL, NULL, NULL, 1417799563, 1417799563),
	    	('updateWorkflow', 2, NULL, NULL, NULL, 1431101273, 1431101273);
    	");
    	
    	$this->execute("
			INSERT INTO `meican_group` (`id`, `name`, `role_name`, `type`) VALUES
			(1, 'Root', 'g1', 'SYSTEM');
		");

    	$this->execute("
			INSERT INTO `meican_auth_item_child` (`parent`, `child`) VALUES
			('g1', 'createGroup'),
			('g1', 'createDomain'),
			('g1', 'createSynchronizer'),
			('g1', 'createUser'),
			('g1', 'updateGroup'),
			('g1', 'updateDomain'),
			('g1', 'updateSynchronizer'),
			('g1', 'updateUser'),
    		('g1', 'updateConfiguration'),
    		('g1', 'readGroup'),
			('g1', 'readDomain'),
			('g1', 'readSynchronizer'),
			('g1', 'readUser'),
    		('g1', 'readConfiguration'),
    		('g1', 'deleteGroup'),
			('g1', 'deleteDomain'),
			('g1', 'deleteSynchronizer'),
			('g1', 'deleteUser');
		");
    	
    	$this->execute("
			INSERT INTO `meican_auth_assignment` (`item_name`, `user_id`, `created_at`) VALUES
			('g1', 1, 1434390581);
		");

    }
    
    

    public function down()
    {
        echo "m150810_183724_diego cannot be reverted.\n";

        return false;
    }

}
