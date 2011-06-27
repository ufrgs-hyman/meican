-- phpMyAdmin SQL Dump
-- version 3.3.2deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 26, 2011 at 05:29 PM
-- Server version: 5.1.41
-- PHP Version: 5.3.2-1ubuntu4.9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `framework`
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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=72 ;

--
-- Dumping data for table `acos`
--

INSERT INTO `acos` (`aco_id`, `obj_id`, `model`, `lft`, `rgt`, `parent_id`) VALUES
(1, 1, 'group_info', 1, 142, NULL),
(2, 1, 'user_info', 2, 3, 1),
(3, 2, 'group_info', 4, 133, 1),
(4, 3, 'group_info', 134, 137, 1),
(5, 4, 'group_info', 138, 141, 1),
(6, 5, 'group_info', 5, 24, 3),
(7, 6, 'group_info', 6, 9, 6),
(8, 7, 'group_info', 10, 13, 6),
(9, 8, 'group_info', 25, 48, 3),
(10, 9, 'group_info', 26, 29, 9),
(11, 10, 'group_info', 30, 33, 9),
(12, 11, 'group_info', 49, 92, 3),
(13, 12, 'group_info', 50, 53, 12),
(14, 13, 'group_info', 54, 57, 12),
(15, 14, 'group_info', 93, 112, 3),
(16, 15, 'group_info', 94, 97, 15),
(17, 16, 'group_info', 98, 101, 15),
(18, 17, 'group_info', 113, 132, 3),
(19, 18, 'group_info', 114, 117, 18),
(20, 19, 'group_info', 118, 121, 18),
(21, 2, 'user_info', 135, 136, 4),
(22, 3, 'user_info', 139, 140, 5),
(23, 4, 'user_info', 7, 8, 7),
(24, 5, 'user_info', 11, 12, 8),
(25, 6, 'user_info', 27, 28, 10),
(26, 7, 'user_info', 31, 32, 11),
(27, 8, 'user_info', 51, 52, 13),
(28, 9, 'user_info', 55, 56, 14),
(29, 10, 'user_info', 95, 96, 16),
(30, 11, 'user_info', 99, 100, 17),
(31, 12, 'user_info', 115, 116, 19),
(32, 13, 'user_info', 119, 120, 20),
(33, 1, 'network_info', 14, 23, 6),
(34, 2, 'network_info', 34, 47, 9),
(35, 3, 'network_info', 58, 91, 12),
(36, 4, 'network_info', 102, 111, 15),
(37, 5, 'network_info', 122, 131, 18),
(38, 1, 'device_info', 15, 18, 33),
(39, 2, 'device_info', 19, 22, 33),
(40, 3, 'device_info', 35, 40, 34),
(41, 4, 'device_info', 41, 46, 34),
(42, 5, 'device_info', 59, 74, 35),
(43, 6, 'device_info', 75, 90, 35),
(44, 7, 'device_info', 103, 106, 36),
(45, 8, 'device_info', 107, 110, 36),
(46, 9, 'device_info', 123, 126, 37),
(47, 10, 'device_info', 127, 130, 37),
(48, 1, 'urn_info', 36, 37, 40),
(49, 2, 'urn_info', 38, 39, 40),
(50, 3, 'urn_info', 42, 43, 41),
(51, 4, 'urn_info', 44, 45, 41),
(52, 5, 'urn_info', 60, 61, 42),
(53, 6, 'urn_info', 62, 63, 42),
(54, 7, 'urn_info', 64, 65, 42),
(55, 8, 'urn_info', 66, 67, 42),
(56, 9, 'urn_info', 68, 69, 42),
(57, 10, 'urn_info', 70, 71, 42),
(58, 11, 'urn_info', 72, 73, 42),
(59, 12, 'urn_info', 76, 77, 43),
(60, 13, 'urn_info', 78, 79, 43),
(61, 14, 'urn_info', 80, 81, 43),
(62, 15, 'urn_info', 82, 83, 43),
(63, 16, 'urn_info', 84, 85, 43),
(64, 17, 'urn_info', 86, 87, 43),
(65, 18, 'urn_info', 88, 89, 43),
(66, 19, 'urn_info', 16, 17, 38),
(67, 20, 'urn_info', 20, 21, 39),
(68, 21, 'urn_info', 104, 105, 44),
(69, 22, 'urn_info', 108, 109, 45),
(70, 23, 'urn_info', 124, 125, 46),
(71, 24, 'urn_info', 128, 129, 47);

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=33 ;

--
-- Dumping data for table `aros`
--

INSERT INTO `aros` (`aro_id`, `obj_id`, `model`, `lft`, `rgt`, `parent_id`) VALUES
(1, 1, 'group_info', 1, 64, NULL),
(2, 1, 'user_info', 2, 3, 1),
(3, 2, 'group_info', 4, 55, 1),
(4, 3, 'group_info', 56, 59, 1),
(5, 4, 'group_info', 60, 63, 1),
(6, 5, 'group_info', 5, 14, 3),
(7, 6, 'group_info', 6, 9, 6),
(8, 7, 'group_info', 10, 13, 6),
(9, 8, 'group_info', 15, 24, 3),
(10, 9, 'group_info', 16, 19, 9),
(11, 10, 'group_info', 20, 23, 9),
(12, 11, 'group_info', 25, 34, 3),
(13, 12, 'group_info', 26, 29, 12),
(14, 13, 'group_info', 30, 33, 12),
(15, 14, 'group_info', 35, 44, 3),
(16, 15, 'group_info', 36, 39, 15),
(17, 16, 'group_info', 40, 43, 15),
(18, 17, 'group_info', 45, 54, 3),
(19, 18, 'group_info', 46, 49, 18),
(20, 19, 'group_info', 50, 53, 18),
(21, 2, 'user_info', 57, 58, 4),
(22, 3, 'user_info', 61, 62, 5),
(23, 4, 'user_info', 7, 8, 7),
(24, 5, 'user_info', 11, 12, 8),
(25, 6, 'user_info', 17, 18, 10),
(26, 7, 'user_info', 21, 22, 11),
(27, 8, 'user_info', 27, 28, 13),
(28, 9, 'user_info', 31, 32, 14),
(29, 10, 'user_info', 37, 38, 16),
(30, 11, 'user_info', 41, 42, 17),
(31, 12, 'user_info', 47, 48, 19),
(32, 13, 'user_info', 51, 52, 20);

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=17 ;

--
-- Dumping data for table `aros_acos`
--

INSERT INTO `aros_acos` (`perm_id`, `aro_id`, `aco_id`, `model`, `create`, `read`, `update`, `delete`) VALUES
(1, 1, 1, NULL, 'deny', 'deny', 'deny', 'deny'),
(2, 2, 1, NULL, 'allow', 'allow', 'allow', 'allow'),
(3, 2, 2, NULL, 'allow', 'allow', 'allow', 'allow'),
(4, 21, 21, '', 'allow', 'allow', 'allow', 'allow'),
(5, 22, 22, '', 'allow', 'allow', 'allow', 'allow'),
(6, 23, 23, '', 'allow', 'allow', 'allow', 'allow'),
(7, 24, 24, '', 'allow', 'allow', 'allow', 'allow'),
(8, 25, 25, '', 'allow', 'allow', 'allow', 'allow'),
(9, 26, 26, '', 'allow', 'allow', 'allow', 'allow'),
(10, 27, 27, '', 'allow', 'allow', 'allow', 'allow'),
(11, 28, 28, '', 'allow', 'allow', 'allow', 'allow'),
(12, 29, 29, '', 'allow', 'allow', 'allow', 'allow'),
(13, 30, 30, '', 'allow', 'allow', 'allow', 'allow'),
(14, 31, 31, '', 'allow', 'allow', 'allow', 'allow'),
(15, 32, 32, '', 'allow', 'allow', 'allow', 'allow'),
(16, 4, 3, '', 'deny', 'allow', 'deny', 'deny');

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
  PRIMARY KEY (`dev_id`),
  KEY `net_id` (`net_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `device_info`
--

INSERT INTO `device_info` (`dev_id`, `dev_descr`, `dev_ip`, `trademark`, `model`, `nr_ports`, `net_id`, `dev_lat`, `dev_lng`) VALUES
(1, 'Switch Extreme UFRGS', '172.16.5.1', 'Extreme', 'Summit X450', 24, 1, NULL, NULL),
(2, 'Switch Cisco UFRGS', '172.16.5.2', 'Cisco', '3560e', 24, 1, NULL, NULL),
(3, 'Switch Cisco UFSC', '172.16.6.1', 'Cisco', '3560e', 24, 2, NULL, NULL),
(4, 'Switch Extreme UFSC', '172.16.6.2', 'Extreme', 'Summit X450', 24, 2, NULL, NULL),
(5, 'Switch Cisco UNIFACS', '172.16.4.1', 'Cisco', '3560e', 24, 3, NULL, NULL),
(6, 'Switch Extreme UNIFACS', '172.16.4.2', 'Extreme', 'Summit X450', 24, 3, NULL, NULL),
(7, 'Switch Extreme UECE', '172.16.13.1', 'Extreme', 'Summit X450', 24, 4, NULL, NULL),
(8, 'Switch Cisco UECE', '172.16.13.2', 'Cisco', '3560e', 24, 4, NULL, NULL),
(9, 'Switch Extreme UFPA', '172.16.10.1', 'Extreme', 'Summit X450', 24, 5, NULL, NULL),
(10, 'Switch Cisco UFPA', '172.16.10.2', 'Cisco', '3560e', 24, 5, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `domain_info`
--

CREATE TABLE IF NOT EXISTS `domain_info` (
  `dom_id` int(11) NOT NULL AUTO_INCREMENT,
  `dom_descr` varchar(30) NOT NULL,
  `dom_ip` varchar(64) NOT NULL,
  `topo_ip` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`dom_ip`),
  UNIQUE KEY `dom_id` (`dom_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `domain_info`
--

INSERT INTO `domain_info` (`dom_id`, `dom_descr`, `dom_ip`, `topo_ip`) VALUES
(2, 'DomÃ­nio Pietro', 'noc.inf.ufrgs.br:65501', ''),
(3, 'DomÃ­nio Juliano', 'noc.inf.ufrgs.br:65502', ''),
(4, 'DomÃ­nio Jair', 'noc.inf.ufrgs.br:65503', ''),
(1, 'DomÃ­nio Local', 'noc.inf.ufrgs.br:65504', ''),
(5, 'DomÃ­nio Leonardo', 'noc.inf.ufrgs.br:65506', '');

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=20 ;

--
-- Dumping data for table `group_info`
--

INSERT INTO `group_info` (`grp_id`, `grp_descr`) VALUES
(1, 'root'),
(2, 'Local Networks'),
(3, 'Maintainer Group'),
(4, 'Engineers Group'),
(5, 'UFRGS Group'),
(6, 'Admins UFRGS'),
(7, 'Users UFRGS'),
(8, 'UFSC Group'),
(9, 'Admins UFSC'),
(10, 'Users UFSC'),
(11, 'UNIFACS Group'),
(12, 'Admins UNIFACS'),
(13, 'Users UNIFACS'),
(14, 'UECE Group'),
(15, 'Admins UECE'),
(16, 'Users UECE'),
(17, 'UFPA Group'),
(18, 'Admins UFPA'),
(19, 'Users UFPA');

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `network_info`
--

INSERT INTO `network_info` (`net_id`, `net_descr`, `net_lat`, `net_lng`) VALUES
(1, 'UFRGS', '-30.04087', '-51.206047'),
(2, 'UFSC', '-27.601672', '-48.518783'),
(3, 'UNIFACS', '-12.985033', '-38.450041'),
(4, 'UECE', '-3.785818', '-38.552459'),
(5, 'UFPA', '-1.474364', '-48.456274');

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=25 ;

--
-- Dumping data for table `urn_info`
--

INSERT INTO `urn_info` (`urn_id`, `urn_string`, `net_id`, `dev_id`, `port`, `vlan`, `max_capacity`, `min_capacity`, `granularity`) VALUES
(1, 'urn:ogf:network:domain=ufsc.cipo.rnp.br:node=UFSC-CIPO-RNP-001:port=6:link=*', 2, 3, 6, '0,3300-3399', 1000000000, 100000000, 100000000),
(2, 'urn:ogf:network:domain=ufsc.cipo.rnp.br:node=UFSC-CIPO-RNP-001:port=7:link=*', 2, 3, 7, '0,3300-3399', 1000000000, 100000000, 100000000),
(3, 'urn:ogf:network:domain=ufsc.cipo.rnp.br:node=UFSC-CIPO-RNP-002:port=6:link=*', 2, 4, 6, '0,3300-3399', 1000000000, 100000000, 100000000),
(4, 'urn:ogf:network:domain=ufsc.cipo.rnp.br:node=UFSC-CIPO-RNP-002:port=7:link=*', 2, 4, 7, '0,3300-3399', 1000000000, 100000000, 100000000),
(5, 'urn:ogf:network:domain=ufsc.cipo.rnp.br:node=UNIFACS-CIPO-RNP-001:port=2:link=*', 3, 5, 2, '0,3300-3399', 1000000000, 100000000, 100000000),
(6, 'urn:ogf:network:domain=ufsc.cipo.rnp.br:node=UNIFACS-CIPO-RNP-001:port=3:link=*', 3, 5, 3, '0,3300-3399', 1000000000, 100000000, 100000000),
(7, 'urn:ogf:network:domain=ufsc.cipo.rnp.br:node=UNIFACS-CIPO-RNP-001:port=4:link=*', 3, 5, 4, '0,3300-3399', 1000000000, 100000000, 100000000),
(8, 'urn:ogf:network:domain=ufsc.cipo.rnp.br:node=UNIFACS-CIPO-RNP-001:port=5:link=*', 3, 5, 5, '0,3300-3399', 1000000000, 100000000, 100000000),
(9, 'urn:ogf:network:domain=ufsc.cipo.rnp.br:node=UNIFACS-CIPO-RNP-001:port=6:link=*', 3, 5, 6, '0,3300-3399', 1000000000, 100000000, 100000000),
(10, 'urn:ogf:network:domain=ufsc.cipo.rnp.br:node=UNIFACS-CIPO-RNP-001:port=7:link=*', 3, 5, 7, '0,3300-3399', 1000000000, 100000000, 100000000),
(11, 'urn:ogf:network:domain=ufsc.cipo.rnp.br:node=UNIFACS-CIPO-RNP-001:port=8:link=*', 3, 5, 8, '0,3300-3399', 1000000000, 100000000, 100000000),
(12, 'urn:ogf:network:domain=ufsc.cipo.rnp.br:node=UNIFACS-CIPO-RNP-002:port=2:link=*', 3, 6, 2, '0,3300-3399', 1000000000, 100000000, 100000000),
(13, 'urn:ogf:network:domain=ufsc.cipo.rnp.br:node=UNIFACS-CIPO-RNP-002:port=3:link=*', 3, 6, 3, '0,3300-3399', 1000000000, 100000000, 100000000),
(14, 'urn:ogf:network:domain=ufsc.cipo.rnp.br:node=UNIFACS-CIPO-RNP-002:port=4:link=*', 3, 6, 4, '0,3300-3399', 1000000000, 100000000, 100000000),
(15, 'urn:ogf:network:domain=ufsc.cipo.rnp.br:node=UNIFACS-CIPO-RNP-002:port=5:link=*', 3, 6, 5, '0,3300-3399', 1000000000, 100000000, 100000000),
(16, 'urn:ogf:network:domain=ufsc.cipo.rnp.br:node=UNIFACS-CIPO-RNP-002:port=6:link=*', 3, 6, 6, '0,3300-3399', 1000000000, 100000000, 100000000),
(17, 'urn:ogf:network:domain=ufsc.cipo.rnp.br:node=UNIFACS-CIPO-RNP-002:port=7:link=*', 3, 6, 7, '0,3300-3399', 1000000000, 100000000, 100000000),
(18, 'urn:ogf:network:domain=ufsc.cipo.rnp.br:node=UNIFACS-CIPO-RNP-002:port=8:link=*', 3, 6, 8, '0,3300-3399', 1000000000, 100000000, 100000000),
(19, 'urn:ogf:network:domain=ufsc.cipo.rnp.br:node=UFRGS-CIPO-RNP-001:port=5:link=*', 1, 1, 5, '0,3300-3399', 1000000000, 100000000, 100000000),
(20, 'urn:ogf:network:domain=ufsc.cipo.rnp.br:node=UFRGS-CIPO-RNP-002:port=5:link=*', 1, 2, 5, '0,3300-3399', 1000000000, 100000000, 100000000),
(21, 'urn:ogf:network:domain=ufsc.cipo.rnp.br:node=UECE-CIPO-RNP-001:port=9:link=*', 4, 7, 9, '0,3300-3399', 1000000000, 100000000, 100000000),
(22, 'urn:ogf:network:domain=ufsc.cipo.rnp.br:node=UECE-CIPO-RNP-002:port=9:link=*', 4, 8, 9, '0,3300-3399', 1000000000, 100000000, 100000000),
(23, 'urn:ogf:network:domain=ufsc.cipo.rnp.br:node=UFPA-CIPO-RNP-001:port=9:link=*', 5, 9, 9, '0,3300-3399', 1000000000, 100000000, 100000000),
(24, 'urn:ogf:network:domain=ufsc.cipo.rnp.br:node=UFPA-CIPO-RNP-002:port=9:link=*', 5, 10, 9, '0,3300-3399', 1000000000, 100000000, 100000000);

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
(1, 1),
(2, 3),
(3, 4),
(4, 6),
(5, 7),
(6, 9),
(7, 10),
(8, 12),
(9, 13),
(10, 15),
(11, 16),
(12, 18),
(13, 19);

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=14 ;

--
-- Dumping data for table `user_info`
--

INSERT INTO `user_info` (`usr_id`, `usr_login`, `usr_password`, `usr_name`, `usr_settings`) VALUES
(1, 'master', '202cb962ac59075b964b07152d234b70', 'Master Administrator', 'date_format=dd/mm/yyyy;language=en_US.utf8'),
(2, 'maintainer', '202cb962ac59075b964b07152d234b70', 'Mantenedor da Topologia', ''),
(3, 'engineer', '202cb962ac59075b964b07152d234b70', 'Engenheiro da Rede', ''),
(4, 'admin_ufrgs', '202cb962ac59075b964b07152d234b70', 'Administrador da UFRGS', ''),
(5, 'user_ufrgs', '202cb962ac59075b964b07152d234b70', 'UsuÃ¡rio da UFRGS', ''),
(6, 'admin_ufsc', '202cb962ac59075b964b07152d234b70', 'Administrador da UFSC', ''),
(7, 'user_ufsc', '202cb962ac59075b964b07152d234b70', 'UsuÃ¡rio da UFSC', ''),
(8, 'admin_unifacs', '202cb962ac59075b964b07152d234b70', 'Administrador da UNIFACS', ''),
(9, 'user_unifacs', '202cb962ac59075b964b07152d234b70', 'UsuÃ¡rio da UNIFACS', ''),
(10, 'admin_uece', '202cb962ac59075b964b07152d234b70', 'Administrador da UECE', ''),
(11, 'user_uece', '202cb962ac59075b964b07152d234b70', 'UsuÃ¡rio da UECE', ''),
(12, 'admin_ufpa', '202cb962ac59075b964b07152d234b70', 'Administrador da UFPA', ''),
(13, 'user_ufpa', '202cb962ac59075b964b07152d234b70', 'UsuÃ¡rio da UFPA', '');
