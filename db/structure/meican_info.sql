-- phpMyAdmin SQL Dump
-- version 3.3.2deb1
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tempo de Geração: Ago 09, 2011 as 03:47 PM
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
-- Estrutura da tabela `meican_info`
--

DROP TABLE IF EXISTS `meican_info`;
CREATE TABLE IF NOT EXISTS `meican_info` (
  `local_domain` tinyint(1) NOT NULL,
  `meican_id` int(11) NOT NULL AUTO_INCREMENT,
  `meican_descr` varchar(30) NOT NULL,
  `meican_ip` varchar(64) NOT NULL,
  `meican_dir_name` varchar(30) NOT NULL,
  PRIMARY KEY (`meican_id`),
  UNIQUE KEY `fed_ip` (`meican_ip`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
