-- phpMyAdmin SQL Dump
-- version 3.3.2deb1
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tempo de Geração: Ago 25, 2011 as 02:43 PM
-- Versão do Servidor: 5.1.41
-- Versão do PHP: 5.3.2-1ubuntu4.9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Banco de Dados: `meican_beta`
--

--
-- Extraindo dados da tabela `domain_info`
--
truncate TABLE `domain_info`;
INSERT INTO `domain_info` (`dom_id`, `dom_descr`, `oscars_ip`, `topology_id`, `ode_ip`, `ode_wsdl_path`) VALUES
(1, 'UFRGS', '200.132.1.28:8085', 'oscars5.ufrgs.br', '', ''),
(2, 'INF', '200.132.1.28:8080', 'oscars2.cipo.rnp.br', '', ''),
(3, 'POPRS', '200.132.1.28:8087', 'oscars7.ufrgs.br', '', '');