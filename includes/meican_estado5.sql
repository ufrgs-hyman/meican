-- phpMyAdmin SQL Dump
-- version 3.3.2deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 15, 2011 at 08:58 PM
-- Server version: 5.1.41
-- PHP Version: 5.3.2-1ubuntu4.9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `meican`
--

-- --------------------------------------------------------

--
-- Table structure for table `acos`
--

CREATE TABLE IF NOT EXISTS `acos` (
  `aco_id` int(11) NOT NULL AUTO_INCREMENT,
  `obj_id` int(11) NOT NULL,
  `model` char(16) NOT NULL,
  `lft` int(11) NOT NULL,
  `rgt` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`aco_id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=35 ;

--
-- Dumping data for table `acos`
--

INSERT INTO `acos` (`aco_id`, `obj_id`, `model`, `lft`, `rgt`, `parent_id`) VALUES
(1, 1, 'root', 1, 68, NULL),
(2, 1, 'topology', 2, 61, 1),
(3, 1, 'aaa', 62, 67, 1),
(4, 1, 'group_info', 63, 66, 3),
(5, 1, 'user_info', 64, 65, 4),
(6, 1, 'domain_info', 3, 34, 2),
(7, 1, 'network_info', 4, 9, 6),
(8, 2, 'network_info', 10, 15, 6),
(9, 3, 'network_info', 16, 21, 6),
(10, 4, 'network_info', 22, 27, 6),
(11, 5, 'network_info', 28, 33, 6),
(12, 1, 'device_info', 5, 6, 7),
(13, 2, 'device_info', 7, 8, 7),
(14, 3, 'device_info', 11, 12, 8),
(15, 4, 'device_info', 13, 14, 8),
(16, 5, 'device_info', 17, 18, 9),
(17, 6, 'device_info', 19, 20, 9),
(18, 7, 'device_info', 23, 24, 10),
(19, 8, 'device_info', 25, 26, 10),
(20, 9, 'device_info', 29, 30, 11),
(21, 10, 'device_info', 31, 32, 11),
(22, 2, 'domain_info', 35, 60, 2),
(23, 6, 'network_info', 36, 41, 22),
(24, 7, 'network_info', 42, 47, 22),
(25, 8, 'network_info', 48, 53, 22),
(26, 9, 'network_info', 54, 59, 22),
(27, 11, 'device_info', 37, 38, 23),
(28, 12, 'device_info', 39, 40, 23),
(29, 13, 'device_info', 43, 44, 24),
(30, 14, 'device_info', 45, 46, 24),
(31, 15, 'device_info', 49, 50, 25),
(32, 16, 'device_info', 51, 52, 25),
(33, 17, 'device_info', 55, 56, 26),
(34, 18, 'device_info', 57, 58, 26);

-- --------------------------------------------------------

--
-- Table structure for table `aros`
--

CREATE TABLE IF NOT EXISTS `aros` (
  `aro_id` int(11) NOT NULL AUTO_INCREMENT,
  `obj_id` int(11) NOT NULL,
  `model` char(16) NOT NULL,
  `lft` int(11) NOT NULL,
  `rgt` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`aro_id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `aros`
--

INSERT INTO `aros` (`aro_id`, `obj_id`, `model`, `lft`, `rgt`, `parent_id`) VALUES
(1, 1, 'group_info', 1, 4, NULL),
(2, 1, 'user_info', 2, 3, 1);

-- --------------------------------------------------------

--
-- Table structure for table `aros_acos`
--

CREATE TABLE IF NOT EXISTS `aros_acos` (
  `perm_id` int(11) NOT NULL AUTO_INCREMENT,
  `aro_id` int(11) NOT NULL,
  `aco_id` int(11) NOT NULL,
  `model` varchar(32) DEFAULT NULL,
  `create` enum('allow','deny') DEFAULT NULL,
  `read` enum('allow','deny') DEFAULT NULL,
  `update` enum('allow','deny') DEFAULT NULL,
  `delete` enum('allow','deny') DEFAULT NULL,
  PRIMARY KEY (`perm_id`),
  UNIQUE KEY `aro_id` (`aro_id`,`aco_id`),
  KEY `aro_id_2` (`aro_id`),
  KEY `aco_id` (`aco_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `aros_acos`
--

INSERT INTO `aros_acos` (`perm_id`, `aro_id`, `aco_id`, `model`, `create`, `read`, `update`, `delete`) VALUES
(1, 1, 1, NULL, 'deny', 'deny', 'deny', 'deny'),
(2, 2, 1, NULL, 'allow', 'allow', 'allow', 'allow');

-- --------------------------------------------------------

--
-- Table structure for table `device_info`
--

CREATE TABLE IF NOT EXISTS `device_info` (
  `dev_id` int(11) NOT NULL AUTO_INCREMENT,
  `dev_descr` char(30) NOT NULL,
  `dev_ip` char(16) NOT NULL,
  `trademark` char(16) DEFAULT NULL,
  `model` char(16) DEFAULT NULL,
  `nr_ports` int(11) DEFAULT NULL,
  `net_id` int(11) NOT NULL,
  `dev_lat` char(10) DEFAULT NULL,
  `dev_lng` char(11) DEFAULT NULL,
  `topo_node_id` varchar(30) NOT NULL,
  PRIMARY KEY (`dev_id`),
  KEY `net_id` (`net_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=19 ;

--
-- Dumping data for table `device_info`
--

INSERT INTO `device_info` (`dev_id`, `dev_descr`, `dev_ip`, `trademark`, `model`, `nr_ports`, `net_id`, `dev_lat`, `dev_lng`, `topo_node_id`) VALUES
(1, 'Switch Cisco', '172.16.5.2', 'Cisco', 'Catalyst 3560e', 24, 1, '', '', 'UFRGS-CIPO-RNP-002'),
(2, 'Switch Extreme', '172.16.5.1', 'Extreme', 'Summit X450', 24, 1, '', '', 'UFRGS-CIPO-RNP-001'),
(3, 'Switch Cisco', '172.16.6.1', 'Cisco', 'Catalyst 3560e', 24, 2, '', '', 'UFSC-CIPO-RNP-001'),
(4, 'Switch Extreme', '172.16.6.2', 'Extreme', 'Summit X450', 24, 2, '', '', 'UFSC-CIPO-RNP-002'),
(5, 'Switch Cisco', '172.16.4.1', 'Cisco', 'Catalyst 3560e', 24, 3, '', '', 'UNIFACS-CIPO-RNP-001'),
(6, 'Switch Extreme', '172.16.4.2', 'Extreme', 'Summit X450', 24, 3, '', '', 'UNIFACS-CIPO-RNP-002'),
(7, 'Switch Extreme', '172.16.13.1', 'Extreme', 'Summit X450', 24, 4, '', '', 'UECE-CIPO-RNP-001'),
(8, 'Switch Cisco', '172.16.13.2', 'Cisco', 'Catalyst 3560e', 24, 4, '', '', 'UECE-CIPO-RNP-002'),
(9, 'Switch Extreme', '172.16.10.1', 'Extreme', 'Summit X450', 24, 5, '', '', 'UFPA-CIPO-RNP-001'),
(10, 'Switch Cisco', '172.16.10.2', 'Cisco', 'Catalyst 3560e', 24, 5, '', '', 'UFPA-CIPO-RNP-002'),
(11, 'Switch Cisco', '172.16.7.1', 'Cisco', 'Catalyst 3560e', 24, 6, '', '', 'CPQD-CIPO-RNP-001'),
(12, 'Switch Extreme', '172.16.7.2', 'Extreme', 'Summit X450', 24, 6, '', '', 'CPQD-CIPO-RNP-002'),
(13, 'Switch Cisco', '172.16.14.1', 'Cisco', 'Catalyst 3560e', 24, 7, '', '', 'RNP-RJ-CIPO-RNP-001'),
(14, 'Switch Extreme', '172.16.14.2', 'Extreme', 'Summit X450', 24, 7, '', '', 'RNP-RJ-CIPO-RNP-002'),
(15, 'Switch Cisco', '172.16.2.1', 'Cisco', 'Catalyst 3560e', 24, 8, '', '', 'UFRJ-CIPO-RNP-001'),
(16, 'Switch Extreme', '172.16.2.2', 'Extreme', 'Summit X450', 24, 8, '', '', 'UFRJ-CIPO-RNP-002'),
(17, 'Switch Cisco', '172.16.12.1', 'Cisco', 'Catalyst 3560e', 24, 9, '', '', 'USP-CIPO-RNP-001'),
(18, 'Switch Extreme', '172.16.12.2', 'Extreme', 'Summit X450', 24, 9, '', '', 'USP-CIPO-RNP-002');

-- --------------------------------------------------------

--
-- Table structure for table `domain_info`
--

CREATE TABLE IF NOT EXISTS `domain_info` (
  `dom_id` int(11) NOT NULL AUTO_INCREMENT,
  `dom_descr` varchar(30) NOT NULL,
  `oscars_ip` varchar(64) NOT NULL,
  `topo_domain_id` varchar(30) NOT NULL,
  PRIMARY KEY (`oscars_ip`),
  UNIQUE KEY `dom_id` (`dom_id`),
  UNIQUE KEY `topo_domain_id` (`topo_domain_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `domain_info`
--

INSERT INTO `domain_info` (`dom_id`, `dom_descr`, `oscars_ip`, `topo_domain_id`) VALUES
(1, 'IpÃª', '200.132.1.28', 'ufsc.cipo.rnp.br'),
(2, 'Giga', '200.132.1.29', 'cpqd.cipo.rnp.br');

-- --------------------------------------------------------

--
-- Table structure for table `federation_info`
--

CREATE TABLE IF NOT EXISTS `federation_info` (
  `fed_id` int(11) NOT NULL AUTO_INCREMENT,
  `fed_descr` varchar(30) NOT NULL,
  `fed_ip` varchar(64) NOT NULL,
  PRIMARY KEY (`fed_id`),
  UNIQUE KEY `fed_ip` (`fed_ip`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `federation_info`
--

INSERT INTO `federation_info` (`fed_id`, `fed_descr`, `fed_ip`) VALUES
(1, 'Backbone RNP', 'noc.inf.ufrgs.br:65504');

-- --------------------------------------------------------

--
-- Table structure for table `flow_info`
--

CREATE TABLE IF NOT EXISTS `flow_info` (
  `flw_id` int(11) NOT NULL AUTO_INCREMENT,
  `flw_name` char(40) NOT NULL,
  `bandwidth` int(11) NOT NULL,
  `src_dom` int(11) NOT NULL,
  `src_urn_string` varchar(128) NOT NULL,
  `src_vlan` int(11) NOT NULL,
  `dst_dom` int(11) NOT NULL,
  `dst_urn_string` varchar(128) NOT NULL,
  `dst_vlan` int(11) NOT NULL,
  PRIMARY KEY (`flw_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `flow_info`
--


-- --------------------------------------------------------

--
-- Table structure for table `gri_info`
--

CREATE TABLE IF NOT EXISTS `gri_info` (
  `gri_id` char(40) NOT NULL,
  `status` enum('ACTIVE','PENDING','FINISHED','CANCELLED','FAILED','ACCEPTED','SUBMITTED','INCREATE','INSETUP','INTEARDOWN','INMODIFY') NOT NULL,
  `res_id` int(11) NOT NULL,
  `start` datetime NOT NULL,
  `finish` datetime NOT NULL,
  PRIMARY KEY (`gri_id`),
  KEY `res_id` (`res_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `gri_info`
--


-- --------------------------------------------------------

--
-- Table structure for table `group_info`
--

CREATE TABLE IF NOT EXISTS `group_info` (
  `grp_id` int(16) NOT NULL AUTO_INCREMENT,
  `grp_descr` char(60) NOT NULL,
  PRIMARY KEY (`grp_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `group_info`
--

INSERT INTO `group_info` (`grp_id`, `grp_descr`) VALUES
(1, 'root');

-- --------------------------------------------------------

--
-- Table structure for table `network_info`
--

CREATE TABLE IF NOT EXISTS `network_info` (
  `net_id` int(11) NOT NULL AUTO_INCREMENT,
  `net_descr` char(30) NOT NULL,
  `net_lat` char(10) NOT NULL,
  `net_lng` char(11) NOT NULL,
  PRIMARY KEY (`net_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `network_info`
--

INSERT INTO `network_info` (`net_id`, `net_descr`, `net_lat`, `net_lng`) VALUES
(1, 'UFRGS', '-30.04087', '-51.206047'),
(2, 'UFSC', '-27.601672', '-48.518783'),
(3, 'UNIFACS', '-12.985033', '-38.450041'),
(4, 'UECE', '-3.785818', '-38.552459'),
(5, 'UFPA', '-1.474364', '-48.456274'),
(6, 'CPQD', '-22.816615', '-47.045585'),
(7, 'RNP-RJ', '-22.95661', '-43.176796'),
(8, 'UFRJ', '-22.862316', '-43.223437'),
(9, 'USP', '-23.557654', '-46.73093');

-- --------------------------------------------------------

--
-- Table structure for table `request_info`
--

CREATE TABLE IF NOT EXISTS `request_info` (
  `loc_id` int(11) NOT NULL AUTO_INCREMENT,
  `req_id` int(11) NOT NULL,
  `dom_src` int(11) DEFAULT NULL,
  `usr_src` int(11) NOT NULL,
  `dom_dst` int(11) DEFAULT NULL,
  `resource_type` varchar(32) DEFAULT NULL,
  `resource_id` int(11) DEFAULT NULL,
  `answerable` enum('yes','no') NOT NULL,
  `status` varchar(128) DEFAULT NULL,
  `response` enum('accept','reject') DEFAULT NULL,
  `message` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`loc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `request_info`
--


-- --------------------------------------------------------

--
-- Table structure for table `reservation_info`
--

CREATE TABLE IF NOT EXISTS `reservation_info` (
  `res_id` int(11) NOT NULL AUTO_INCREMENT,
  `res_name` char(40) NOT NULL,
  `flw_id` int(11) NOT NULL,
  `tmr_id` int(11) NOT NULL,
  PRIMARY KEY (`res_id`),
  KEY `flow_id` (`flw_id`),
  KEY `timer_id` (`tmr_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `reservation_info`
--


-- --------------------------------------------------------

--
-- Table structure for table `timer_info`
--

CREATE TABLE IF NOT EXISTS `timer_info` (
  `tmr_id` int(11) NOT NULL AUTO_INCREMENT,
  `tmr_name` char(40) NOT NULL,
  `start` datetime NOT NULL,
  `finish` datetime NOT NULL,
  `freq` enum('DAILY','WEEKLY','MONTHLY') DEFAULT NULL,
  `until` datetime DEFAULT NULL,
  `count` int(11) DEFAULT NULL,
  `interval` int(11) DEFAULT NULL,
  `byday` set('SU','MO','TU','WE','TH','FR','SA') DEFAULT NULL,
  `summary` text,
  PRIMARY KEY (`tmr_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `timer_info`
--


-- --------------------------------------------------------

--
-- Table structure for table `urn_info`
--

CREATE TABLE IF NOT EXISTS `urn_info` (
  `urn_id` int(11) NOT NULL AUTO_INCREMENT,
  `urn_string` char(128) NOT NULL,
  `net_id` int(11) NOT NULL,
  `dev_id` int(11) NOT NULL,
  `port` int(11) NOT NULL,
  `vlan` char(32) NOT NULL,
  `max_capacity` bigint(20) NOT NULL,
  `min_capacity` bigint(20) NOT NULL,
  `granularity` bigint(20) NOT NULL,
  PRIMARY KEY (`urn_id`),
  UNIQUE KEY `urn_string` (`urn_string`),
  UNIQUE KEY `device_id` (`dev_id`,`port`),
  KEY `network_id` (`net_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `urn_info`
--


-- --------------------------------------------------------

--
-- Table structure for table `user_group`
--

CREATE TABLE IF NOT EXISTS `user_group` (
  `usr_id` int(16) NOT NULL,
  `grp_id` int(16) NOT NULL,
  PRIMARY KEY (`usr_id`,`grp_id`),
  KEY `usr_id` (`usr_id`),
  KEY `grp_id` (`grp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_group`
--

INSERT INTO `user_group` (`usr_id`, `grp_id`) VALUES
(1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_info`
--

CREATE TABLE IF NOT EXISTS `user_info` (
  `usr_id` int(16) NOT NULL AUTO_INCREMENT,
  `usr_login` char(30) NOT NULL,
  `usr_password` char(32) DEFAULT NULL,
  `usr_name` char(60) DEFAULT NULL,
  `usr_settings` mediumtext,
  PRIMARY KEY (`usr_id`),
  UNIQUE KEY `usr_login` (`usr_login`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `user_info`
--

INSERT INTO `user_info` (`usr_id`, `usr_login`, `usr_password`, `usr_name`, `usr_settings`) VALUES
(1, 'master', '202cb962ac59075b964b07152d234b70', 'Master Administrator', 'date_format=dd/mm/yyyy;language=en_US.utf8');
