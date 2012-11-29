-- phpMyAdmin SQL Dump
-- version 3.3.2deb1ubuntu1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 29, 2012 at 12:22 PM
-- Server version: 5.1.66
-- PHP Version: 5.3.5-1ubuntu7.2ppa1~lucid1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `meican`
--

-- --------------------------------------------------------

--
-- Table structure for table `workflows_info`
--

CREATE TABLE IF NOT EXISTS `workflows_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `working` longtext NOT NULL,
  `language` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`,`language`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
