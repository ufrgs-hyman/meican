<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

use yii\db\Schema;
use yii\db\Migration;

class m150901_152747_diego extends Migration
{
    public function up()
    {
    	$this->execute("
			ALTER TABLE `meican_bpm_node` CHANGE `type` `type` ENUM('New_Request','Duration','Domain','User','Bandwidth','Request_User_Authorization','Request_Group_Authorization','Accept_Automatically','Deny_Automatically','Hour','WeekDay','Group','Device') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
        ");
    	
    	$this->execute("
			ALTER TABLE `meican_bpm_flow_control` CHANGE `type` `type` ENUM('New_Request','Duration','Domain','User','Bandwidth','Request_User_Authorization','Request_Group_Authorization','Accept_Automatically','Deny_Automatically','Hour','WeekDay','Group','Device') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
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
    		('createWaypoint', 2, NULL, NULL, NULL, 1431101274, 1431101274),
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
    		('g2', 1, NULL, NULL, NULL, 1418403082, 1418403082),
    		('g3', 1, NULL, NULL, NULL, 1418403083, 1418403083),
    		('g4', 1, NULL, NULL, NULL, 1418403084, 1418403084),
    		('g5', 1, NULL, NULL, NULL, 1418403085, 1418403085),
    		('g6', 1, NULL, NULL, NULL, 1418403086, 1418403086),
    		('g7', 1, NULL, NULL, NULL, 1418403087, 1418403088),
    		('g8', 1, NULL, NULL, NULL, 1418403088, 1418403087),
    		('g9', 1, NULL, NULL, NULL, 1418403089, 1418403089),
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
			(1, 'Root', 'g1', 'SYSTEM'),
    		(2, 'Admin', 'g2', 'DOMAIN'),
			(3, 'Requester', 'g3', 'DOMAIN'),
			(4, 'Requester with Waypoints', 'g4', 'DOMAIN'),
    		(5, 'Reservations Editor', 'g5', 'DOMAIN'),
			(6, 'Workflows Editor', 'g6', 'DOMAIN'),
			(7, 'Topology Editor', 'g7', 'DOMAIN'),
			(8, 'Automated Tests Editor', 'g8', 'DOMAIN'),
			(9, 'Roles Editor', 'g9', 'DOMAIN');
		");
    	
    	$this->execute("
			INSERT INTO `meican_auth_item_child` (`parent`, `child`) VALUES
			('g1', 'createDomain'),
			('g2', 'createDomainTopology'),
			('g7', 'createDomainTopology'),
			('g1', 'createGroup'),
			('g2', 'createReservation'),
			('g3', 'createReservation'),
			('g5', 'createReservation'),
			('g4', 'createReservation'),
			('g9', 'createRole'),
			('g2', 'createRole'),
			('g1', 'createSynchronizer'),
			('g2', 'createTest'),
			('g8', 'createTest'),
			('g1', 'createUser'),
			('g2', 'createWaypoint'),
			('g4', 'createWaypoint'),
			('g2', 'createWorkflow'),
			('g6', 'createWorkflow'),
			('g1', 'deleteDomain'),
			('g2', 'deleteDomainTopology'),
			('g7', 'deleteDomainTopology'),
			('g1', 'deleteGroup'),
			('g2', 'deleteReservation'),
			('g5', 'deleteReservation'),
			('g9', 'deleteRole'),
			('g2', 'deleteRole'),
			('g1', 'deleteSynchronizer'),
			('g2', 'deleteTest'),
			('g8', 'deleteTest'),
			('g1', 'deleteUser'),
			('g2', 'deleteWorkflow'),
			('g6', 'deleteWorkflow'),
			('g1', 'readConfiguration'),
			('g1', 'readDomain'),
			('g2', 'readDomainTopology'),
			('g7', 'readDomainTopology'),
			('g1', 'readGroup'),
			('g2', 'readReservation'),
			('g5', 'readReservation'),
			('g9', 'readRole'),
			('g2', 'readRole'),
			('g1', 'readSynchronizer'),
			('g2', 'readTest'),
			('g8', 'readTest'),
			('g1', 'readUser'),
			('g2', 'readWorkflow'),
			('g6', 'readWorkflow'),
			('g1', 'updateConfiguration'),
			('g1', 'updateDomain'),
			('g2', 'updateDomainTopology'),
			('g7', 'updateDomainTopology'),
			('g1', 'updateGroup'),
			('g2', 'updateReservation'),
			('g5', 'updateReservation'),
			('g9', 'updateRole'),
			('g2', 'updateRole'),
			('g1', 'updateSynchronizer'),
			('g2', 'updateTest'),
			('g8', 'updateTest'),
			('g1', 'updateUser'),
			('g2', 'updateWorkflow'),
			('g6', 'updateWorkflow');
		");
    	 
    	$this->execute("
			INSERT INTO `meican_auth_assignment` (`item_name`, `user_id`, `created_at`) VALUES
			('g1', 1, 1434390581);
		");
    }

    
     public function down()
    {
        echo "m150901_152747_diego cannot be reverted.\n";

        return false;
    }
    
    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }
    
    public function safeDown()
    {
    }
    */
}
