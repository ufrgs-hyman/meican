<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

use yii\db\Schema;
use yii\db\Migration;

class m150707_212428_mqg extends Migration
{
    public function up()
    {
        $this->execute("
            DELETE FROM `meican_provider` WHERE 1;");
        $this->execute("
            ALTER TABLE `meican_device` CHANGE `name` `name` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL;");
        $this->execute("
            ALTER TABLE `meican_device` CHANGE `node` `node` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_bin NULL;");
        $this->execute("
            ALTER TABLE `meican_domain` DROP `oscars_version`;");
        $this->execute("
            ALTER TABLE `meican_domain` DROP FOREIGN KEY `domain_provider`;");
        $this->execute("
            ALTER TABLE `meican_provider` DROP `connection_url`;

            ALTER TABLE `meican_provider` CHANGE `nsa` `nsa` VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL;

            ALTER TABLE `meican_provider` ADD `name` VARCHAR(100) NOT NULL AFTER `type`;");
        $this->execute("
            ALTER TABLE `meican_provider` DROP `discovery_url`;");
        $this->execute("
            ALTER TABLE `meican_provider` ADD `latitude` FLOAT NULL AFTER `nsa`;");
        $this->execute("
            ALTER TABLE `meican_provider` ADD `longitude` FLOAT NULL AFTER `latitude`;");
        $this->execute("
            ALTER TABLE `meican_device` ADD `domain_id` INT NOT NULL AFTER `network_id`;

            ALTER TABLE `meican_device` ADD `address` VARCHAR(200) NULL AFTER `model`;");
        $this->execute("
            ALTER TABLE `meican_device` DROP FOREIGN KEY `fk_network`;");
        $this->execute("
            ALTER TABLE `meican_device` DROP `network_id`;");
        $this->execute("
            ALTER TABLE `meican_provider` CHANGE `type` `type` ENUM('AGG','UPA','DUMMY') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;");
        $this->execute("
            DROP TABLE meican_aggregator;");
        $this->execute("
            ALTER TABLE `meican_network` ADD `urn` VARCHAR(250) NOT NULL AFTER `name`;");
        $this->execute("
            CREATE TABLE IF NOT EXISTS `meican_service` (
            `id` int(11) NOT NULL,
              `provider_id` int(11) NOT NULL,
              `type` enum('NSI_CONNECTION','NSI_DISCOVERY','NSI_TOPOLOGY') NOT NULL,
              `url` varchar(250) NOT NULL
            ) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
            ");
        $this->execute("
            ALTER TABLE `meican_service`
            ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `provider_id_2` (`provider_id`,`url`), ADD KEY `provider_id` (`provider_id`);");
        $this->execute("
            ALTER TABLE `meican_service`
            MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;");
        $this->execute("
            ALTER TABLE `meican_service`
            ADD CONSTRAINT `provider_service` FOREIGN KEY (`provider_id`) REFERENCES `meican_provider` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;");
        $this->execute("
            CREATE TABLE IF NOT EXISTS `meican_port` (
            `id` int(11) NOT NULL,
              `type` enum('NSI','NMWG') NOT NULL,
              `directionality` enum('BI','UNI_IN','UNI_OUT') NOT NULL,
              `urn` varchar(250) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
              `name` varchar(100) NOT NULL,
              `max_capacity` int(11) DEFAULT NULL,
              `min_capacity` int(11) DEFAULT NULL,
              `granularity` int(11) DEFAULT NULL,
              `biport_id` int(11) DEFAULT NULL,
              `alias_id` int(11) DEFAULT NULL,
              `device_id` int(11) NOT NULL,
              `network_id` int(11) DEFAULT NULL
            ) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
            ");
        $this->execute("
            ALTER TABLE `meican_port`
             ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `value` (`urn`), ADD KEY `fk_device_idx` (`device_id`), ADD KEY `alias_urn_id` (`alias_id`), ADD KEY `network_id` (`network_id`), ADD KEY `biport_id` (`biport_id`);
            ");
        $this->execute("
            ALTER TABLE `meican_port`
            MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;");
        $this->execute("
            ALTER TABLE `meican_port`
            ADD CONSTRAINT `biport_uniport` FOREIGN KEY (`biport_id`) REFERENCES `meican_port` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
            ADD CONSTRAINT `device_port` FOREIGN KEY (`device_id`) REFERENCES `meican_device` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
            ADD CONSTRAINT `net_port` FOREIGN KEY (`network_id`) REFERENCES `meican_network` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
            ADD CONSTRAINT `port_alias` FOREIGN KEY (`alias_id`) REFERENCES `meican_port` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;");
        $this->execute("
            CREATE TABLE IF NOT EXISTS `meican_preference` (
              `name` varchar(200) NOT NULL,
              `value` text NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ");
        $this->execute("
            INSERT INTO `meican_preference` (`name`, `value`) VALUES
            ('circuits.default.provider.nsa', 'dummy'),
            ('circuits.uniport.enabled', 'false'),
            ('meican.nsa', '');");
        $this->execute("
            ALTER TABLE `meican_preference`
            ADD PRIMARY KEY (`name`);");
        $this->execute("
                ALTER TABLE `meican_domain` DROP `topology`;");
        $this->execute("
                ALTER TABLE `meican_domain` CHANGE `name` `name` VARCHAR(60) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL;");
        $this->execute("
                ALTER TABLE `meican_vlan_range` DROP FOREIGN KEY `urn_vlan`;");
        $this->execute("
                DROP TABLE meican_urn;");
        $this->execute("
                ALTER TABLE `meican_network` DROP FOREIGN KEY `network_domain`;");
        $this->execute("
                ALTER TABLE `meican_domain` ADD UNIQUE(`name`);
                ");
        $this->execute("
                ALTER TABLE `meican_domain` DROP FOREIGN KEY `domain_workflow`;");
        $this->execute("
                ALTER TABLE `meican_domain` DROP `workflow_id`;");
        $this->execute("
                ALTER TABLE `meican_user_domain` DROP FOREIGN KEY `user_domain_domain`;");
        $this->execute("
                ALTER TABLE `meican_domain` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT;");
        $this->execute("
                ALTER TABLE `meican_network` ADD CONSTRAINT `dom_net` FOREIGN KEY (`domain_id`) REFERENCES `meican_domain`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;
                ");
        $this->execute("
                ALTER TABLE `meican_vlan_range` CHANGE `urn_id` `port_id` INT(11) NOT NULL;");
        $this->execute("
                ALTER TABLE `meican_vlan_range` ADD CONSTRAINT `port_vlan` FOREIGN KEY (`port_id`) REFERENCES `meican_port`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;");
        $this->execute("
                ALTER TABLE `meican_device` ADD CONSTRAINT `dom_dev` FOREIGN KEY (`domain_id`) REFERENCES `meican_domain`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;");
        $this->execute("
                ALTER TABLE `meican_user_domain` ADD CONSTRAINT `dom_user_role` FOREIGN KEY (`domain_id`) REFERENCES `meican_domain`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;
                ");
        $this->execute("
                ALTER TABLE `meican_network` ADD UNIQUE(`urn`);");
        $this->execute("
                ALTER TABLE `meican_network` ADD `address` VARCHAR(200) NULL AFTER `urn`;");
        $this->execute("
                ALTER TABLE meican_device DROP INDEX node;");
        $this->execute("
                ALTER TABLE `meican_device` ADD UNIQUE( `node`, `domain_id`);");
        $this->execute("
                ALTER TABLE `meican_reservation` DROP FOREIGN KEY `reservation_provider`;");
        $this->execute("
                ALTER TABLE `meican_reservation` DROP `provider_id`;");
        $this->execute("
                ALTER TABLE `meican_reservation_path` DROP `domain`;");
        $this->execute("
                ALTER TABLE `meican_reservation_path` DROP `device`;");
        $this->execute("
                ALTER TABLE `meican_reservation_path` DROP `port`;");
        $this->execute("
                ALTER TABLE `meican_reservation_path` CHANGE `urn` `port_urn` VARCHAR(250) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;");
        $this->execute("
                ALTER TABLE `meican_reservation` ADD `requester_nsa` VARCHAR(250) NOT NULL AFTER `finish`;");
        $this->execute("
                ALTER TABLE `meican_reservation` ADD `provider_nsa` VARCHAR(250) NOT NULL AFTER `requester_nsa`;");
    }

    public function down()
    {
        echo "m150707_212428_mqg cannot be reverted.\n";

        return false;
    }
}
