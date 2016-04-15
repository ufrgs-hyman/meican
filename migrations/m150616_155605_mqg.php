<?php
/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

use yii\db\Schema;
use yii\db\Migration;


/**
*
* Database Schema for MEICAN
*
* @since 2.1
**/

class m150616_155605_mqg extends Migration {
	
    public function up() {
   		$this->execute("
CREATE TABLE IF NOT EXISTS `meican_aggregator` (
  `id` int(11) NOT NULL,
  `default` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		");
   		$this->execute("
CREATE TABLE IF NOT EXISTS `meican_auth_assignment` (
  `item_name` varchar(64) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		");
   		$this->execute("
INSERT INTO `meican_auth_assignment` (`item_name`, `user_id`, `created_at`) VALUES
('g1', 1, 1434390581);
");
   		$this->execute("
CREATE TABLE IF NOT EXISTS `meican_auth_item` (
  `name` varchar(64) NOT NULL,
  `type` int(11) NOT NULL,
  `description` text,
  `rule_name` varchar(64) DEFAULT NULL,
  `data` text,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");
   		$this->execute("
INSERT INTO `meican_auth_item` (`name`, `type`, `description`, `rule_name`, `data`, `created_at`, `updated_at`) VALUES
('createGroup', 2, NULL, NULL, NULL, 1417803786, 1417803786),
('createReservation', 2, NULL, NULL, NULL, 1417799563, 1417799563),
('createTests', 2, NULL, NULL, NULL, 1431109580, 1431109580),
('createTopology', 2, NULL, NULL, NULL, 1417799563, 1417799563),
('createUser', 2, NULL, NULL, NULL, 1417799563, 1417799563),
('createWorkflow', 2, NULL, NULL, NULL, 1431101273, 1431101273),
('deleteGroup', 2, NULL, NULL, NULL, 1417803786, 1417803786),
('deleteReservation', 2, NULL, NULL, NULL, 1417799563, 1417799563),
('deleteTests', 2, NULL, NULL, NULL, 1431109580, 1431109580),
('deleteTopology', 2, NULL, NULL, NULL, 1417799563, 1417799563),
('deleteUser', 2, NULL, NULL, NULL, 1417799563, 1417799563),
('deleteWorkflow', 2, NULL, NULL, NULL, 1431101273, 1431101273),
('g1', 1, NULL, NULL, NULL, 1418403081, 1418403081),
('readGroup', 2, NULL, NULL, NULL, 1417803786, 1417803786),
('readReservation', 2, NULL, NULL, NULL, 1417802547, 1417802547),
('readTests', 2, NULL, NULL, NULL, 1431109580, 1431109580),
('readTopology', 2, NULL, NULL, NULL, 1417803786, 1417803786),
('readUser', 2, NULL, NULL, NULL, 1417803786, 1417803786),
('readWorkflow', 2, NULL, NULL, NULL, 1431101273, 1431101273),
('updateAggregator', 2, NULL, NULL, NULL, 1431109580, 1431109580),
('updateGroup', 2, NULL, NULL, NULL, 1417803786, 1417803786),
('updateReservation', 2, NULL, NULL, NULL, 1417799563, 1417799563),
('updateTests', 2, NULL, NULL, NULL, 1431109580, 1431109580),
('updateTopology', 2, NULL, NULL, NULL, 1417799563, 1417799563),
('updateUser', 2, NULL, NULL, NULL, 1417799563, 1417799563),
('updateWorkflow', 2, NULL, NULL, NULL, 1431101273, 1431101273);
");
   		$this->execute("
CREATE TABLE IF NOT EXISTS `meican_auth_item_child` (
  `parent` varchar(64) NOT NULL,
  `child` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");
   		$this->execute("
INSERT INTO `meican_auth_item_child` (`parent`, `child`) VALUES
('g1', 'createGroup'),
('g1', 'createReservation'),
('g1', 'createTopology'),
('g1', 'createUser'),
('g1', 'createWorkflow'),
('g1', 'deleteGroup'),
('g1', 'deleteReservation'),
('g1', 'deleteTopology'),
('g1', 'deleteUser'),
('g1', 'deleteWorkflow'),
('g1', 'readGroup'),
('g1', 'readReservation'),
('g1', 'readTopology'),
('g1', 'readUser'),
('g1', 'readWorkflow'),
('g1', 'updateGroup'),
('g1', 'updateReservation'),
('g1', 'updateTopology'),
('g1', 'updateUser'),
('g1', 'updateWorkflow');
");
   		$this->execute("
CREATE TABLE IF NOT EXISTS `meican_auth_rule` (
  `name` varchar(64) NOT NULL,
  `data` text,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");
   		$this->execute("
CREATE TABLE IF NOT EXISTS `meican_automated_test` (
  `id` int(11) NOT NULL,
  `crontab_id` varchar(30) DEFAULT NULL,
  `crontab_changed` tinyint(1) NOT NULL,
  `crontab_frequency` varchar(30) NOT NULL,
  `status` enum('ENABLED','DISABLED','DELETED','PROCESSING') NOT NULL,
  `frequency_type` enum('DAILY','WEEKLY','MONTHLY') NOT NULL,
  `last_execution` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");
   		$this->execute("
CREATE TABLE IF NOT EXISTS `meican_bpm_flow_control` (
`id` int(11) NOT NULL,
  `connection_id` int(11) NOT NULL,
  `workflow_id` int(11) NOT NULL,
  `domain_id` int(11) NOT NULL,
  `node_id` int(11) NOT NULL,
  `type` enum('New_Request','Duration','Domain','User','Bandwidth','Request_User_Authorization',
  	'Request_Group_Authorization','Accept_Automatically','Deny_Automatically','Hour','WeekDay') NOT NULL,
  `value` text,
  `operator` text,
  `status` enum('READY','WAITING','YES','NO') NOT NULL DEFAULT 'READY'
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
");
   		$this->execute("
CREATE TABLE IF NOT EXISTS `meican_bpm_node` (
`id` int(11) NOT NULL,
  `workflow_id` int(11) NOT NULL,
  `type` enum('New_Request','Duration','Domain','User','Bandwidth','Request_User_Authorization',
  	'Request_Group_Authorization','Accept_Automatically','Deny_Automatically','Hour','WeekDay') NOT NULL,
  `operator` text,
  `value` text,
  `index` int(11) NOT NULL,
  `output_yes` int(11) DEFAULT NULL,
  `output_no` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
		");
   		$this->execute("
CREATE TABLE IF NOT EXISTS `meican_bpm_workflow` (
`id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `domain_id` int(11) NOT NULL,
  `json` text NOT NULL,
  `active` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
		");
   		$this->execute("
CREATE TABLE IF NOT EXISTS `meican_connection` (
`id` int(11) NOT NULL,
  `external_id` varchar(65) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `status` enum('PENDING','CREATED','CONFIRMED','SUBMITTED','PROVISIONED','CANCEL REQUESTED','CANCELLED',
  	'FAILED ON CREATE','FAILED ON CONFIRM','FAILED ON SUBMIT','FAILED ON PROVISION') NOT NULL,
  `dataplane_status` enum('INACTIVE','ACTIVE') NOT NULL,
  `auth_status` enum('EXPIRED','WAITING','AUTHORIZED','DENIED') NOT NULL,
  `start` datetime NOT NULL,
  `finish` datetime NOT NULL,
  `reservation_id` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
		");
   		$this->execute("
CREATE TABLE IF NOT EXISTS `meican_connection_auth` (
`id` int(11) NOT NULL,
  `domain_id` int(11) NOT NULL,
  `status` enum('WAITING','AUTHORIZED','DENIED','EXPIRED') NOT NULL,
  `type` enum('USER','GROUP','WORKFLOW') NOT NULL,
  `manager_message` varchar(200) DEFAULT NULL,
  `manager_user_id` int(11) DEFAULT NULL,
  `manager_group_id` int(11) DEFAULT NULL,
  `manager_workflow_id` int(11) DEFAULT NULL,
  `connection_id` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
		");
   		$this->execute("
CREATE TABLE IF NOT EXISTS `meican_connection_path` (
  `conn_id` int(11) NOT NULL,
  `path_order` int(11) NOT NULL,
  `domain` varchar(50) NOT NULL,
  `src_urn` varchar(150) NOT NULL,
  `src_vlan` varchar(20) NOT NULL,
  `dst_urn` varchar(150) NOT NULL,
  `dst_vlan` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		");
   		$this->execute("
CREATE TABLE IF NOT EXISTS `meican_device` (
`id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `ip` varchar(16) DEFAULT NULL,
  `trademark` varchar(50) DEFAULT NULL,
  `model` varchar(50) DEFAULT NULL,
  `latitude` float DEFAULT NULL,
  `longitude` float DEFAULT NULL,
  `node` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `network_id` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
		");
   		$this->execute("
CREATE TABLE IF NOT EXISTS `meican_domain` (
  `id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `topology` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `oscars_version` enum('0.6') NOT NULL,
  `workflow_id` int(11) DEFAULT NULL,
  `default_policy` enum('ACCEPT_ALL','REJECT_ALL') NOT NULL DEFAULT 'ACCEPT_ALL'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		");
   		$this->execute("
CREATE TABLE IF NOT EXISTS `meican_group` (
`id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `role_name` varchar(64) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
		");
   		$this->execute("
INSERT INTO `meican_group` (`id`, `name`, `role_name`) VALUES
(1, 'Master', 'g1');
		");
   		$this->execute("
CREATE TABLE IF NOT EXISTS `meican_network` (
`id` int(11) NOT NULL,
  `name` varchar(60) NOT NULL,
  `latitude` float DEFAULT NULL,
  `longitude` float DEFAULT NULL,
  `domain_id` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
		");
   		$this->execute("
CREATE TABLE IF NOT EXISTS `meican_provider` (
`id` int(11) NOT NULL,
  `type` enum('AGGREGATOR','BRIDGE','DUMMY') NOT NULL,
  `nsa` varchar(200) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `connection_url` varchar(300) DEFAULT NULL,
  `discovery_url` varchar(300) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
		");
   		$this->execute("
CREATE TABLE IF NOT EXISTS `meican_reservation` (
`id` int(11) NOT NULL,
  `type` enum('NORMAL','TEST') NOT NULL,
  `name` varchar(50) NOT NULL,
  `date` datetime NOT NULL,
  `bandwidth` int(11) NOT NULL,
  `start` datetime NOT NULL,
  `finish` datetime NOT NULL,
  `provider_id` int(11) NOT NULL,
  `request_user_id` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
		");
   		$this->execute("
CREATE TABLE IF NOT EXISTS `meican_reservation_path` (
  `reservation_id` int(11) NOT NULL,
  `path_order` int(11) NOT NULL,
  `urn` varchar(150) NOT NULL,
  `domain` varchar(50) NOT NULL,
  `device` varchar(50) NOT NULL,
  `port` varchar(50) DEFAULT NULL,
  `vlan` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		");
   		$this->execute("
CREATE TABLE IF NOT EXISTS `meican_reservation_recurrence` (
  `id` int(11) NOT NULL,
  `type` enum('D','W','M') NOT NULL,
  `every` int(11) NOT NULL,
  `weekdays` set('SU','MO','TU','WE','TH','FR','SA') DEFAULT NULL,
  `finish` datetime DEFAULT NULL,
  `occurrence_limit` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		");
   		$this->execute("
CREATE TABLE IF NOT EXISTS `meican_urn` (
`id` int(11) NOT NULL,
  `value` varchar(150) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `port` varchar(50) DEFAULT NULL,
  `max_capacity` bigint(20) DEFAULT NULL,
  `min_capacity` bigint(20) DEFAULT NULL,
  `granularity` bigint(20) DEFAULT NULL,
  `device_id` int(11) NOT NULL,
  `alias_urn_id` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
		");
   		$this->execute("
CREATE TABLE IF NOT EXISTS `meican_user` (
`id` int(11) NOT NULL,
  `login` varchar(30) NOT NULL,
  `password` varchar(200) NOT NULL,
  `authkey` varchar(100) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
		");
		$this->insert('meican_user', [
            'id' => 1,
            'login' => 'master',
            'password' => '$2y$13$HlKOEje1Mtckn79tYjdLAOmzSC7unR/RJg6O9mz42hggMuX.3TuPq',
            'authkey' => 'YWuulnFca89gxqSYdUfCRi-vUN7QYO-G',
        ]);
   		$this->execute("
CREATE TABLE IF NOT EXISTS `meican_user_domain` (
`id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `domain_id` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
		");
   		$this->execute("
INSERT INTO `meican_user_domain` (`id`, `user_id`, `domain_id`) VALUES
(1, 1, NULL);
		");
   		$this->execute("
CREATE TABLE IF NOT EXISTS `meican_user_settings` (
  `id` int(11) NOT NULL,
  `language` enum('en-US','pt-BR') NOT NULL,
  `date_format` varchar(10) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		");
   		$this->execute("
INSERT INTO `meican_user_settings` (`id`, `language`, `date_format`, `name`, `email`) VALUES
(1, 'en-US', 'mm/dd/yyyy', 'master', 'master');
		");
   		$this->execute("
CREATE TABLE IF NOT EXISTS `meican_vlan_range` (
`id` int(11) NOT NULL,
  `value` varchar(30) NOT NULL,
  `urn_id` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
		");
   	  	$this->execute("
ALTER TABLE `meican_aggregator`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `default` (`default`);
   		");
      	$this->execute("
ALTER TABLE `meican_auth_assignment`
 ADD PRIMARY KEY (`item_name`,`user_id`), ADD KEY `assign_user_domain` (`user_id`);
    	");
      	$this->execute("
ALTER TABLE `meican_auth_item`
 ADD PRIMARY KEY (`name`), ADD KEY `rule_name` (`rule_name`), ADD KEY `type` (`type`);
    	");
      	$this->execute("
ALTER TABLE `meican_auth_item_child`
 ADD PRIMARY KEY (`parent`,`child`), ADD KEY `child` (`child`);
    	");
      	$this->execute("
ALTER TABLE `meican_auth_rule`
 ADD PRIMARY KEY (`name`);
    	");
      	$this->execute("
ALTER TABLE `meican_automated_test`
 ADD PRIMARY KEY (`id`);
    	");
      	$this->execute("
ALTER TABLE `meican_bpm_flow_control`
 ADD PRIMARY KEY (`id`), ADD KEY `reservation_id` (`connection_id`), ADD KEY `workflow_id` (`workflow_id`), 
 ADD KEY `source_id` (`node_id`), ADD KEY `domain_id` (`domain_id`), ADD KEY `connection_id` (`connection_id`);
    	");
      	$this->execute("
ALTER TABLE `meican_bpm_node`
 ADD PRIMARY KEY (`id`), ADD KEY `workflow_id` (`workflow_id`), ADD KEY `output_yes` (`output_yes`), ADD KEY `output_no` (`output_no`);
    	");
      	$this->execute("
ALTER TABLE `meican_bpm_workflow`
 ADD PRIMARY KEY (`id`), ADD KEY `domain_id` (`domain_id`), ADD KEY `name` (`name`), ADD KEY `active` (`active`);
    	");
      	$this->execute("
ALTER TABLE `meican_connection`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `external_id` (`external_id`), ADD KEY `fk_gri_reservation_idx` (`reservation_id`);
    	");
      	$this->execute("
ALTER TABLE `meican_connection_auth`
 ADD PRIMARY KEY (`id`), ADD KEY `manager_user_id` (`manager_user_id`), ADD KEY `manager_group_id` (`manager_group_id`), 
 ADD KEY `connection_id` (`connection_id`), ADD KEY `manager_workflow_id` (`manager_workflow_id`), ADD KEY `domain_id` (`domain_id`);
    	");
      	$this->execute("
ALTER TABLE `meican_connection_path`
 ADD PRIMARY KEY (`conn_id`,`path_order`), ADD KEY `path_order` (`path_order`), ADD KEY `conn_id` (`conn_id`), ADD KEY `domain` (`domain`);
    	");
      	$this->execute("
ALTER TABLE `meican_device`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `node` (`node`,`network_id`), ADD KEY `fk_network_idx` (`network_id`);
    	");
      	$this->execute("
ALTER TABLE `meican_domain`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `topology_id` (`topology`), ADD KEY `workflow_id` (`workflow_id`);
    	");
      	$this->execute("
ALTER TABLE `meican_group`
 ADD PRIMARY KEY (`id`), ADD KEY `role_name` (`role_name`);
    	");
      	$this->execute("
ALTER TABLE `meican_network`
 ADD PRIMARY KEY (`id`), ADD KEY `fk_domain_idx` (`domain_id`);
    	");
      	$this->execute("
ALTER TABLE `meican_provider`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `nsa` (`nsa`), ADD KEY `type` (`type`);
    	");
      	$this->execute("
ALTER TABLE `meican_reservation`
 ADD PRIMARY KEY (`id`), ADD KEY `provider_id` (`provider_id`), ADD KEY `type` (`type`), 
 ADD KEY `request_user_id` (`request_user_id`);
    	");
      	$this->execute("
ALTER TABLE `meican_reservation_path`
 ADD PRIMARY KEY (`reservation_id`,`path_order`), ADD KEY `fk_meican_flow_path_meican_flow1_idx` (`reservation_id`);
    	");
      	$this->execute("
ALTER TABLE `meican_reservation_recurrence`
 ADD PRIMARY KEY (`id`);
    	");
      	$this->execute("
ALTER TABLE `meican_urn`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `value` (`value`), ADD KEY `fk_device_idx` (`device_id`), ADD KEY `alias_urn_id` (`alias_urn_id`);
    	");
      	$this->execute("
ALTER TABLE `meican_user`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `login_UNIQUE` (`login`), ADD UNIQUE KEY `authkey` (`authkey`);
    	");
      	$this->execute("
ALTER TABLE `meican_user_domain`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `user_id` (`user_id`,`domain_id`), ADD KEY `domain_id` (`domain_id`);
    	");
      	$this->execute("
ALTER TABLE `meican_user_settings`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `email` (`email`);
    	");
      	$this->execute("
ALTER TABLE `meican_vlan_range`
 ADD PRIMARY KEY (`id`), ADD KEY `urn_id` (`urn_id`);
    	");
      	$this->execute("
ALTER TABLE `meican_bpm_flow_control`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
    	");
      	$this->execute("
ALTER TABLE `meican_bpm_node`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
    	");
      	$this->execute("
ALTER TABLE `meican_bpm_workflow`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
    	");
      	$this->execute("
ALTER TABLE `meican_connection`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
    	");
      	$this->execute("
ALTER TABLE `meican_connection_auth`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
    	");
      	$this->execute("
ALTER TABLE `meican_device`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
    	");
      	$this->execute("
ALTER TABLE `meican_group`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
    	");
      	$this->execute("
ALTER TABLE `meican_network`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
    	");
      	$this->execute("
ALTER TABLE `meican_provider`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
    	");
      	$this->execute("
ALTER TABLE `meican_reservation`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
    	");
      	$this->execute("
ALTER TABLE `meican_urn`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
    	");
      	$this->execute("
ALTER TABLE `meican_user`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
    	");
      	$this->execute("
ALTER TABLE `meican_user_domain`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
    	");
      	$this->execute("
ALTER TABLE `meican_vlan_range`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
    	");
      	$this->execute("
ALTER TABLE `meican_aggregator`
ADD CONSTRAINT `aggregator_provider` FOREIGN KEY (`id`) REFERENCES `meican_provider` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
    	");
      	$this->execute("
ALTER TABLE `meican_auth_assignment`
ADD CONSTRAINT `assign_user_domain` FOREIGN KEY (`user_id`) REFERENCES `meican_user_domain` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `meican_auth_assignment_ibfk_1` FOREIGN KEY (`item_name`) REFERENCES `meican_auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE;
    	");
      	$this->execute("
ALTER TABLE `meican_auth_item`
ADD CONSTRAINT `meican_auth_item_ibfk_1` FOREIGN KEY (`rule_name`) REFERENCES `meican_auth_rule` (`name`) ON DELETE SET NULL ON UPDATE CASCADE;
    	");
      	$this->execute("
ALTER TABLE `meican_auth_item_child`
ADD CONSTRAINT `meican_auth_item_child_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `meican_auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `meican_auth_item_child_ibfk_2` FOREIGN KEY (`child`) REFERENCES `meican_auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE;
    	");
      	$this->execute("
ALTER TABLE `meican_automated_test`
ADD CONSTRAINT `test_reservation` FOREIGN KEY (`id`) REFERENCES `meican_reservation` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
    	");
      	$this->execute("
ALTER TABLE `meican_bpm_flow_control`
ADD CONSTRAINT `bpm_flow_connection` FOREIGN KEY (`connection_id`) REFERENCES `meican_connection` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `bpm_flow_domain` FOREIGN KEY (`domain_id`) REFERENCES `meican_domain` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `bpm_flow_node` FOREIGN KEY (`node_id`) REFERENCES `meican_bpm_node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `bpm_flow_workflow` FOREIGN KEY (`workflow_id`) REFERENCES `meican_bpm_workflow` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
    	");
      	$this->execute("
ALTER TABLE `meican_bpm_node`
ADD CONSTRAINT `bpm_node_workflow` FOREIGN KEY (`workflow_id`) REFERENCES `meican_bpm_workflow` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `bpm_outno_node` FOREIGN KEY (`output_no`) REFERENCES `meican_bpm_node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `bpm_outyes_node` FOREIGN KEY (`output_yes`) REFERENCES `meican_bpm_node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
    	");
      	$this->execute("
ALTER TABLE `meican_bpm_workflow`
ADD CONSTRAINT `bpm_workflow_domain` FOREIGN KEY (`domain_id`) REFERENCES `meican_domain` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
    	");
      	$this->execute("
ALTER TABLE `meican_connection`
ADD CONSTRAINT `reservation_connection` FOREIGN KEY (`reservation_id`) REFERENCES `meican_reservation` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
    	");
      	$this->execute("
ALTER TABLE `meican_connection_auth`
ADD CONSTRAINT `manager_connection` FOREIGN KEY (`connection_id`) REFERENCES `meican_connection` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `manager_domain` FOREIGN KEY (`domain_id`) REFERENCES `meican_domain` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `manager_group` FOREIGN KEY (`manager_group_id`) REFERENCES `meican_group` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
ADD CONSTRAINT `manager_user` FOREIGN KEY (`manager_user_id`) REFERENCES `meican_user` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
ADD CONSTRAINT `manager_workflow` FOREIGN KEY (`manager_workflow_id`) REFERENCES `meican_bpm_workflow` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;
    	");
      	$this->execute("
ALTER TABLE `meican_connection_path`
ADD CONSTRAINT `conn_path` FOREIGN KEY (`conn_id`) REFERENCES `meican_connection` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
    	");
      	$this->execute("
ALTER TABLE `meican_device`
ADD CONSTRAINT `fk_network` FOREIGN KEY (`network_id`) REFERENCES `meican_network` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
    	");
      	$this->execute("
ALTER TABLE `meican_domain`
ADD CONSTRAINT `domain_provider` FOREIGN KEY (`id`) REFERENCES `meican_provider` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `domain_workflow` FOREIGN KEY (`workflow_id`) REFERENCES `meican_bpm_workflow` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;
    	");
      	$this->execute("
ALTER TABLE `meican_group`
ADD CONSTRAINT `auth_item_group` FOREIGN KEY (`role_name`) REFERENCES `meican_auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE;
    	");
      	$this->execute("
ALTER TABLE `meican_network`
ADD CONSTRAINT `network_domain` FOREIGN KEY (`domain_id`) REFERENCES `meican_domain` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
    	");
      	$this->execute("
ALTER TABLE `meican_reservation`
ADD CONSTRAINT `reservation_provider` FOREIGN KEY (`provider_id`) REFERENCES `meican_provider` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `reservation_requester` FOREIGN KEY (`request_user_id`) REFERENCES `meican_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
    	");
      	$this->execute("
ALTER TABLE `meican_reservation_path`
ADD CONSTRAINT `res_path` FOREIGN KEY (`reservation_id`) REFERENCES `meican_reservation` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
    	");
      	$this->execute("
ALTER TABLE `meican_reservation_recurrence`
ADD CONSTRAINT `reservation_recurrence` FOREIGN KEY (`id`) REFERENCES `meican_reservation` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
    	");
      	$this->execute("
ALTER TABLE `meican_urn`
ADD CONSTRAINT `alias_urn` FOREIGN KEY (`alias_urn_id`) REFERENCES `meican_urn` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
ADD CONSTRAINT `fk_device` FOREIGN KEY (`device_id`) REFERENCES `meican_device` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
    	");
      	$this->execute("
ALTER TABLE `meican_user_domain`
ADD CONSTRAINT `user_domain_domain` FOREIGN KEY (`domain_id`) REFERENCES `meican_domain` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `user_domain_user` FOREIGN KEY (`user_id`) REFERENCES `meican_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
    	");
      	$this->execute("
ALTER TABLE `meican_user_settings`
ADD CONSTRAINT `fk_user_setttings` FOREIGN KEY (`id`) REFERENCES `meican_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
    	");
      	$this->execute("
ALTER TABLE `meican_vlan_range`
ADD CONSTRAINT `urn_vlan` FOREIGN KEY (`urn_id`) REFERENCES `meican_urn` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
    	");
    }
    
    public function down() {
    	echo "m150616_155605_mqg cannot be reverted.\n";
    	
    	return false;
    }
}
