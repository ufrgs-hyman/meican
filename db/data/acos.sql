-- phpMyAdmin SQL Dump
-- version 3.3.2deb1
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tempo de Geração: Ago 25, 2011 as 03:16 PM
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

--
-- Extraindo dados da tabela `acos`
--
truncate TABLE `acos`;
INSERT INTO `acos` (`aco_id`, `obj_id`, `model`, `lft`, `rgt`, `parent_id`) VALUES
(1, NULL, 'root', 1, 32, NULL),
(2, NULL, 'topology', 2, 13, 1),
(3, NULL, 'domain_info', 3, 12, 2),
(4, NULL, 'network_info', 4, 11, 3),
(5, NULL, 'device_info', 5, 10, 4),
(6, NULL, 'urn_info', 6, 9, 5),
(7, NULL, 'reservation_info', 7, 8, 6),
(8, NULL, 'aaa', 14, 25, 1),
(9, NULL, 'group_info', 15, 18, 8),
(10, NULL, 'user_info', 16, 17, 9),
(11, NULL, 'acl', 26, 27, 1),
(12, NULL, 'bpm', 28, 29, 1),
(13, NULL, 'may_specify_path', 30, 31, 1),
(14, 1, 'group_info', 19, 24, 8),
(15, 2, 'group_info', 20, 23, 13),
(16, 1, 'user_info', 21, 22, 14);
