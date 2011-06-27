-- phpMyAdmin SQL Dump
-- version 3.3.2deb1
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tempo de Geração: Mai 04, 2011 as 04:00 PM
-- Versão do Servidor: 5.1.41
-- Versão do PHP: 5.3.2-1ubuntu4.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Banco de Dados: `framework`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `device_info`
--

CREATE TABLE IF NOT EXISTS `device_info` (
  `dev_id` int(11) NOT NULL AUTO_INCREMENT,
  `dev_descr` char(30) NOT NULL,
  `dev_ip` char(16) NOT NULL,
  `trademark` char(16) DEFAULT NULL,
  `model` char(16) DEFAULT NULL,
  `nr_ports` int(11) DEFAULT NULL,
  `net_id` int(11) NOT NULL,
  `dev_lat` float NOT NULL,
  `dev_lng` float NOT NULL,
  PRIMARY KEY (`dev_id`),
  KEY `net_id` (`net_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Extraindo dados da tabela `device_info`
--

INSERT INTO `device_info` (`dev_id`, `dev_descr`, `dev_ip`, `trademark`, `model`, `nr_ports`, `net_id`, `dev_lat`, `dev_lng`) VALUES
(1, 'Switch Extreme U', '192.168.0.26', 'Extreme', 'Summit X450e', 24, 2, -30.0686, -51.1199),
(2, 'Switch Cisco UFR', '192.168.0.28', 'Cisco', 'Catalyst 3560e', 24, 2, -30.0685, -51.1198),
(3, 'Switch Extreme U', '192.168.1.56', 'Extreme', 'Summit X450e', 24, 3, -27.6009, -48.5228),
(4, 'Switch Cisco UFS', '192.168.1.57', 'Cisco', 'Catalyst 3560e', 24, 3, -27.6008, -48.5228);

-- --------------------------------------------------------

--
-- Estrutura da tabela `domain_info`
--

DROP TABLE IF EXISTS `domain_info`;
CREATE TABLE IF NOT EXISTS `domain_info` (
  `dom_id` int(11) NOT NULL AUTO_INCREMENT,
  `dom_descr` varchar(16) NOT NULL,
  `dom_ip` varchar(64) NOT NULL,
  `topo_ip` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`dom_ip`),
  UNIQUE KEY `dom_id` (`dom_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Extraindo dados da tabela `domain_info`
--

INSERT INTO `domain_info` (`dom_id`, `dom_descr`, `dom_ip`, `topo_ip`) VALUES
(1, 'Pietro Dom', 'noc.inf.ufrgs.br:65501', NULL),
(4, 'Juliano Dom', 'noc.inf.ufrgs.br:65502', NULL),
(2, 'Jair Dom', 'noc.inf.ufrgs.br:65503', NULL),
(3, 'Felipe Dom', 'noc.inf.ufrgs.br:65504', NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `network_info`
--

CREATE TABLE IF NOT EXISTS `network_info` (
  `net_id` int(11) NOT NULL AUTO_INCREMENT,
  `net_descr` char(16) NOT NULL,
  `net_lat` float NOT NULL,
  `net_lng` float NOT NULL,
  PRIMARY KEY (`net_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Extraindo dados da tabela `network_info`
--

INSERT INTO `network_info` (`net_id`, `net_descr`, `net_lat`, `net_lng`) VALUES
(2, 'UFRGS', -30.0166, -51.2166),
(3, 'UFSC', -27.5833, -48.5333),
(4, 'UFRJ', -22.9, -43.2),
(5, 'UFPA', -1.45, -48.5);

-- --------------------------------------------------------

--
-- Estrutura da tabela `urn_info`
--

DROP TABLE IF EXISTS `urn_info`;
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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

--
-- Extraindo dados da tabela `urn_info`
--

INSERT INTO `urn_info` (`urn_id`, `urn_string`, `net_id`, `dev_id`, `port`, `vlan`, `max_capacity`, `min_capacity`, `granularity`) VALUES
(1, 'urn:ogf:network:domain=ufsc.cipo.rnp.br:node=UFSC', 3, 2, 5, '0,3000', 100000, 100, 100),
(5, 'urn:ogf:network:domain=ufsc.cipo.rnp.br:node=UFSC-CIPO-RNP-001:port=6:link=*', 3, 2, 6, '0,3300-3399', 0, 0, 0),
(6, 'urn:ogf:network:domain=ufsc.cipo.rnp.br:node=UFSC-CIPO-RNP-001:port=7:link=*', 2, 1, 7, '0,3300-3399', 0, 0, 0),
(8, 'urn:ogf:network:domain=ufsc.cipo.rnp.br:node=UNIFACS-CIPO-RNP-001:port=8:link=*', 2, 1, 8, '0,3300-3399', 1000000000, 100000000, 100000000),
(9, 'blablalblablabla', 2, 3, 6, '2323', 123213, 123123213, 123131);
