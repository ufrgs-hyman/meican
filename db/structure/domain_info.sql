-- phpMyAdmin SQL Dump
-- version 3.3.2deb1
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tempo de Geração: Ago 09, 2011 as 03:48 PM
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
-- Estrutura da tabela `domain_info`
--

DROP TABLE IF EXISTS `domain_info`;
CREATE TABLE IF NOT EXISTS `domain_info` (
  `dom_id` int(11) NOT NULL AUTO_INCREMENT,
  `dom_descr` varchar(30) NOT NULL,
  `oscars_ip` varchar(64) NOT NULL,
  `topology_id` varchar(30) NOT NULL,
  `ode_ip` varchar(128) DEFAULT NULL,
  `ode_wsdl_path` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`dom_id`),
  UNIQUE KEY `dom_descr` (`dom_descr`),
  UNIQUE KEY `oscars_ip` (`oscars_ip`),
  UNIQUE KEY `topology_id` (`topology_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
