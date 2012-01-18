-- phpMyAdmin SQL Dump
-- version 3.3.2deb1
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tempo de Geração: Ago 10, 2011 as 05:22 PM
-- Versão do Servidor: 5.1.41
-- Versão do PHP: 5.3.2-1ubuntu4.9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Banco de Dados: `meican`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `gri_info`
--

DROP TABLE IF EXISTS `gri_info`;
CREATE TABLE IF NOT EXISTS `gri_info` (
  `gri_id` int(11) NOT NULL AUTO_INCREMENT,
  `gri_descr` char(40) NOT NULL,
  `status` enum('ACTIVE','PENDING','FINISHED','CANCELLED','FAILED','ACCEPTED','SUBMITTED','INCREATE','INSETUP','INTEARDOWN','INMODIFY') NOT NULL,
  `start` datetime NOT NULL,
  `finish` datetime NOT NULL,
  `dom_id` int(11) NOT NULL,
  `res_id` int(11) NOT NULL,
  `send` tinyint(1) NOT NULL,
  PRIMARY KEY (`gri_id`),
  UNIQUE KEY `gri_descr` (`gri_descr`),
  KEY `res_id` (`res_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;
