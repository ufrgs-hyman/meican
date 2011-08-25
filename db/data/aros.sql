-- phpMyAdmin SQL Dump
-- version 3.3.2deb1
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tempo de Geração: Ago 25, 2011 as 03:33 PM
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
-- Extraindo dados da tabela `aros`
--

INSERT INTO `aros` (`aro_id`, `obj_id`, `model`, `lft`, `rgt`, `parent_id`) VALUES
(1, 1, 'group_info', 1, 4, NULL),
(2, 1, 'user_info', 2, 3, 1);