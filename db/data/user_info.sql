-- phpMyAdmin SQL Dump
-- version 3.3.2deb1
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tempo de Geração: Ago 25, 2011 as 03:30 PM
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
-- Extraindo dados da tabela `user_info`
--
truncate TABLE `user_info`;
INSERT INTO `user_info` (`usr_id`, `usr_login`, `usr_password`, `usr_name`, `usr_email`, `usr_settings`) VALUES
(1, 'master', '202cb962ac59075b964b07152d234b70', 'Master Administrator', NULL, 'date_format=dd/mm/yyyy;language=en_US.utf8');