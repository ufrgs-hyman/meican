-- phpMyAdmin SQL Dump
-- version 3.3.2deb1
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tempo de Geração: Abr 08, 2011 as 04:39 PM
-- Versão do Servidor: 5.1.41
-- Versão do PHP: 5.3.2-1ubuntu4.7

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT=0;
START TRANSACTION;


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Banco de Dados: `framework`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `acos`
--

DROP TABLE IF EXISTS `acos`;
CREATE TABLE IF NOT EXISTS `acos` (
  `aco_id` int(11) NOT NULL AUTO_INCREMENT,
  `obj_id` int(11) NOT NULL,
  `model` char(16) NOT NULL,
  `lft` int(11) NOT NULL,
  `rgt` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`aco_id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Extraindo dados da tabela `acos`
--

INSERT INTO `acos` (`aco_id`, `obj_id`, `model`, `lft`, `rgt`, `parent_id`) VALUES
(1, 1, 'container', 1, 6, NULL),
(2, 1, 'group_info', 2, 3, 1),
(3, 1, 'user_info', 4, 5, 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `aros`
--

DROP TABLE IF EXISTS `aros`;
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
-- Extraindo dados da tabela `aros`
--

INSERT INTO `aros` (`aro_id`, `obj_id`, `model`, `lft`, `rgt`, `parent_id`) VALUES
(1, 1, 'group_info', 1, 4, NULL),
(2, 1, 'user_info', 2, 3, 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `aros_acos`
--

DROP TABLE IF EXISTS `aros_acos`;
CREATE TABLE IF NOT EXISTS `aros_acos` (
  `perm_id` int(11) NOT NULL AUTO_INCREMENT,
  `aro_id` int(11) NOT NULL,
  `aco_id` int(11) NOT NULL,
  `create` enum('allow','deny') DEFAULT NULL,
  `read` enum('allow','deny') DEFAULT NULL,
  `update` enum('allow','deny') DEFAULT NULL,
  `delete` enum('allow','deny') DEFAULT NULL,
  PRIMARY KEY (`perm_id`),
  UNIQUE KEY `aro_id` (`aro_id`,`aco_id`),
  KEY `aro_id_2` (`aro_id`),
  KEY `aco_id` (`aco_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=29 ;

--
-- Extraindo dados da tabela `aros_acos`
--

INSERT INTO `aros_acos` (`perm_id`, `aro_id`, `aco_id`, `create`, `read`, `update`, `delete`) VALUES
(1, 1, 1, 'allow', 'allow', 'allow', 'allow');

-- --------------------------------------------------------

--
-- Estrutura da tabela `group_info`
--

DROP TABLE IF EXISTS `group_info`;
CREATE TABLE IF NOT EXISTS `group_info` (
  `grp_id` int(16) NOT NULL AUTO_INCREMENT,
  `grp_descr` char(60) NOT NULL,
  PRIMARY KEY (`grp_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Extraindo dados da tabela `group_info`
--

INSERT INTO `group_info` (`grp_id`, `grp_descr`) VALUES
(1, 'root');

-- --------------------------------------------------------

--
-- Estrutura da tabela `user_group`
--

DROP TABLE IF EXISTS `user_group`;
CREATE TABLE IF NOT EXISTS `user_group` (
  `usr_id` int(16) NOT NULL,
  `grp_id` int(16) NOT NULL,
  PRIMARY KEY (`usr_id`,`grp_id`),
  KEY `usr_id` (`usr_id`),
  KEY `grp_id` (`grp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `user_group`
--

INSERT INTO `user_group` (`usr_id`, `grp_id`) VALUES
(1, 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `user_info`
--

DROP TABLE IF EXISTS `user_info`;
CREATE TABLE IF NOT EXISTS `user_info` (
  `usr_id` int(16) NOT NULL AUTO_INCREMENT,
  `usr_login` char(30) NOT NULL,
  `usr_password` char(32) DEFAULT NULL,
  `usr_name` char(60) DEFAULT NULL,
  `usr_settings` mediumtext,
  PRIMARY KEY (`usr_id`),
  UNIQUE KEY `usr_login` (`usr_login`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Extraindo dados da tabela `user_info`
--

INSERT INTO `user_info` (`usr_id`, `usr_login`, `usr_password`, `usr_name`, `usr_settings`) VALUES
(1, 'pfbiasuz', '202cb962ac59075b964b07152d234b70', 'Pietro Biasuz', 'date_format=dd/mm/yyyy;language=pt_BR.utf8');

--
-- Restrições para as tabelas dumpadas
--

----
---- Restrições para a tabela `acos`
----
--ALTER TABLE `acos`
--  ADD CONSTRAINT `acos_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `acos` (`aco_id`) ON DELETE SET NULL ON UPDATE SET NULL;
--
----
---- Restrições para a tabela `aros`
----
--ALTER TABLE `aros`
--  ADD CONSTRAINT `aros_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `aros` (`aro_id`) ON DELETE SET NULL ON UPDATE SET NULL;
--
----
---- Restrições para a tabela `aros_acos`
----
--ALTER TABLE `aros_acos`
--  ADD CONSTRAINT `aros_acos_ibfk_1` FOREIGN KEY (`aro_id`) REFERENCES `aros` (`aro_id`) ON DELETE CASCADE ON UPDATE CASCADE,
--  ADD CONSTRAINT `aros_acos_ibfk_2` FOREIGN KEY (`aco_id`) REFERENCES `acos` (`aco_id`) ON DELETE CASCADE ON UPDATE CASCADE;
--
----
---- Restrições para a tabela `user_group`
----
--ALTER TABLE `user_group`
--  ADD CONSTRAINT `user_group_ibfk_1` FOREIGN KEY (`usr_id`) REFERENCES `user_info` (`usr_id`) ON DELETE CASCADE ON UPDATE CASCADE,
--  ADD CONSTRAINT `user_group_ibfk_2` FOREIGN KEY (`grp_id`) REFERENCES `group_info` (`grp_id`) ON DELETE CASCADE ON UPDATE CASCADE;
--COMMIT;