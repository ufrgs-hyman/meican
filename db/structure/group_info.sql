-- phpMyAdmin SQL Dump
-- version 3.3.2deb1
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tempo de Geração: Ago 25, 2011 as 03:52 PM
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
-- Estrutura da tabela `group_info`
--

DROP TABLE IF EXISTS `group_info`;
CREATE TABLE IF NOT EXISTS `group_info` (
  `grp_id` int(16) NOT NULL AUTO_INCREMENT,
  `grp_descr` char(60) NOT NULL,
  PRIMARY KEY (`grp_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;