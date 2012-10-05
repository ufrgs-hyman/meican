-- phpMyAdmin SQL Dump
-- version 3.3.2deb1
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tempo de Geração: Ago 09, 2011 as 04:35 PM
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
-- Estrutura da tabela `request_info`
--

DROP TABLE IF EXISTS `request_info`;
CREATE TABLE IF NOT EXISTS `request_info` (
  `loc_id` int(11) NOT NULL AUTO_INCREMENT,
  `req_id` int(11) NOT NULL,
  `src_meican_ip` varchar(64) NOT NULL,
  `src_dom_id` int(11) NOT NULL,
  `src_usr` int(11) NOT NULL,
  `dst_meican_ip` varchar(64) NOT NULL,
  `dst_dom_id` int(11) NOT NULL,
  `resource_type` varchar(32) DEFAULT NULL,
  `resource_id` int(11) DEFAULT NULL,
  `answerable` enum('yes','no') NOT NULL,
  `status` varchar(128) DEFAULT NULL,
  `response` enum('accept','reject') DEFAULT NULL,
  `message` varchar(128) DEFAULT NULL,
  `crr_meican_ip` varchar(64) DEFAULT NULL,
  `crr_dom_id` int(11) DEFAULT NULL,
  `response_user` int(11) DEFAULT NULL,
  `start_time` float DEFAULT NULL,
  `finish_time` float DEFAULT NULL,
  PRIMARY KEY (`loc_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;
