-- phpMyAdmin SQL Dump
-- version 3.3.2deb1
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tempo de Geração: Abr 06, 2011 as 05:31 PM
-- Versão do Servidor: 5.1.41
-- Versão do PHP: 5.3.2-1ubuntu4.7

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
-- Estrutura da tabela `request_info`
--

DROP TABLE IF EXISTS `request_info`;

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Extraindo dados da tabela `request_info`
--


--
-- Restrições para as tabelas dumpadas
--

--
-- Restrições para a tabela `request_info`
--
--ALTER TABLE `request_info`
--  ADD CONSTRAINT `request_info_ibfk_2` FOREIGN KEY (`dom_dst`) REFERENCES `domain_info` (`dom_ip`) ON DELETE CASCADE ON UPDATE CASCADE,
--  ADD CONSTRAINT `request_info_ibfk_1` FOREIGN KEY (`dom_src`) REFERENCES `domain_info` (`dom_ip`) ON DELETE CASCADE ON UPDATE CASCADE;