-- phpMyAdmin SQL Dump
-- version 3.3.2deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 09, 2011 at 03:38 PM
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
-- Table structure for table `flow_info`
--

DROP TABLE IF EXISTS `flow_info`;
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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16 ;

--
-- Dumping data for table `flow_info`
--

INSERT INTO `flow_info` (`flw_id`, `flw_name`, `bandwidth`, `src_dom`, `src_urn_string`, `src_vlan`, `dst_dom`, `dst_urn_string`, `dst_vlan`) VALUES
(1, 'Fluxo teste', 100, 1, 'urn:teste', 0, 1, 'urn:ogf:network:domain=ufsc.cipo.rnp.br:node=UFSC-CIPO-RNP-001:port=6:link=*', 105),
(2, 'Fluxo teste Mysql', 100, 1, 'urn:ogf:network:domain=ufsc.cipo.rnp.br:node=UFSC-CIPO-RNP-001:port=7:link=*', 0, 1, 'urn:teste', 100),
(5, 'Mais um flow', 100, 2, 'blablalblablabla', 0, 1, 'urn:ogf:network:domain=ufsc.cipo.rnp.br:node=UFSC-CIPO-RNP-001:port=7:link=*', 0),
(6, 'Fluxo teste', 100, 2, 'urn:ogf:network:domain=ufsc.cipo.rnp.br:node=UFSC-CIPO-RNP-001:port=7:link=*', 0, 2, 'urn:ogf:network:domain=ufsc.cipo.rnp.br:node=UFSC', 0),
(7, 'Fluxo agora com nome', 200, 1, 'urn:ogf:network:domain=ufsc.cipo.rnp.br:node=UFSC-CIPO-RNP-001:port=7:link=*', 0, 1, 'urn:ogf:network:domain=ufsc.cipo.rnp.br:node=UFSC-CIPO-RNP-001:port=7:link=*', 0),
(9, 'teste', 100, 1, 'urn:teste', 0, 2, 'blablalblablabla', 0),
(12, 'flow novo again2', 100, 1, 'urn:ogf:network:domain=ufsc.cipo.rnp.br:node=UNIFACS-CIPO-RNP-001:port=8:link=*', 0, 1, 'urn:teste', 0),
(13, 'Mais um', 100, 1, 'urn:teste', 0, 2, 'urn:ogf:network:domain=ufsc.cipo.rnp.br:node=UFSC-CIPO-RNP-001:port=6:link=*', 0),
(14, 'Mais um de novo', 100, 1, 'urn:teste', 0, 3, 'urn:ogf:network:domain=ufsc.cipo.rnp.br:node=UFSC', 0),
(15, 'fluxo pra reserva', 100, 1, 'urn:ogf:network:domain=ufsc.cipo.rnp.br:node=UFSC-CIPO-RNP-001:port=6:link=*', 0, 2, 'blablalblablabla', 2323);

-- --------------------------------------------------------

--
-- Table structure for table `gri_info`
--

DROP TABLE IF EXISTS `gri_info`;
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

INSERT INTO `gri_info` (`gri_id`, `status`, `res_id`, `start`, `finish`) VALUES
('gri-oscars-1', 'ACTIVE', 1, '2011-05-09 12:00:00', '2011-05-09 12:30:00'),
('gri-oscars-2', 'PENDING', 1, '2011-05-10 12:00:00', '2011-05-10 12:30:00'),
('gri-oscars-3', 'FAILED', 1, '2011-05-11 12:00:00', '2011-05-11 12:30:00'),
('GRI-OSCARS-4', 'FINISHED', 2, '2011-05-02 15:34:00', '2011-05-02 15:39:00');

-- --------------------------------------------------------

--
-- Table structure for table `reservation_info`
--

DROP TABLE IF EXISTS `reservation_info`;
CREATE TABLE IF NOT EXISTS `reservation_info` (
  `res_id` int(11) NOT NULL AUTO_INCREMENT,
  `res_name` char(40) NOT NULL,
  `flw_id` int(11) NOT NULL,
  `tmr_id` int(11) NOT NULL,
  PRIMARY KEY (`res_id`),
  KEY `flow_id` (`flw_id`),
  KEY `timer_id` (`tmr_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `reservation_info`
--

INSERT INTO `reservation_info` (`res_id`, `res_name`, `flw_id`, `tmr_id`) VALUES
(1, 'Primeira reserva', 15, 25),
(2, 'Default reservation name', 5, 18);

-- --------------------------------------------------------

--
-- Table structure for table `timer_info`
--

DROP TABLE IF EXISTS `timer_info`;
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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=26 ;

--
-- Dumping data for table `timer_info`
--

INSERT INTO `timer_info` (`tmr_id`, `tmr_name`, `start`, `finish`, `freq`, `until`, `count`, `interval`, `byday`, `summary`) VALUES
(9, 'Sem recorrencia', '2011-05-02 15:00:00', '2011-05-02 15:30:00', '', '0000-00-00 00:00:00', NULL, NULL, '', ''),
(10, 'Recorrencia diaria', '2011-05-02 15:01:00', '2011-05-02 15:06:00', 'DAILY', '2011-05-14 23:59:00', NULL, 2, '', 'Repeat every 2 days, until 14/05/2011'),
(11, 'Recorrencia semanal', '2011-05-02 15:13:00', '2011-05-02 15:18:00', 'WEEKLY', '0000-00-00 00:00:00', 5, 2, 'MO,TH,FR', 'Repeat every 2 weeks on Monday, Thursday, Friday, 5 times'),
(18, 'no Recorrencia mensal', '2011-05-02 15:34:00', '2011-05-02 15:39:00', '', '0000-00-00 00:00:00', NULL, NULL, '', 'NULL'),
(19, 'Mensal 2', '2011-05-02 15:35:00', '2011-05-02 15:40:00', 'MONTHLY', '2011-07-31 23:59:00', NULL, 2, '', 'Repeat every 2 months, until 31/07/2011'),
(23, 'Timer na reserva', '2011-05-04 19:58:00', '2011-05-04 20:03:00', '', '0000-00-00 00:00:00', NULL, NULL, '', ''),
(24, 'novo timer', '2011-05-05 18:00:00', '2011-05-05 19:00:00', 'DAILY', '0000-00-00 00:00:00', 4, 1, '', 'Repeat every day, 4 times'),
(25, 'timer pra reserva', '2011-05-05 19:21:00', '2011-05-05 19:26:00', 'WEEKLY', '2011-05-31 23:59:00', NULL, 1, 'TH', 'Repeat every week on Thursday, until 31/05/2011');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `gri_info`
--
--ALTER TABLE `gri_info`
--  ADD CONSTRAINT `gri_info_ibfk_1` FOREIGN KEY (`res_id`) REFERENCES `reservation_info` (`res_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `reservation_info`
--
--ALTER TABLE `reservation_info`
--  ADD CONSTRAINT `reservation_info_ibfk_1` FOREIGN KEY (`flw_id`) REFERENCES `flow_info` (`flw_id`) ON DELETE CASCADE ON UPDATE CASCADE,
--  ADD CONSTRAINT `reservation_info_ibfk_2` FOREIGN KEY (`tmr_id`) REFERENCES `timer_info` (`tmr_id`) ON DELETE CASCADE ON UPDATE CASCADE;
