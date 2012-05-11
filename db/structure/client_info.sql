-- phpMyAdmin SQL Dump
-- version 3.3.2deb1ubuntu1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 10, 2012 at 08:15 PM
-- Server version: 5.1.62
-- PHP Version: 5.3.5-1ubuntu7.2ppa1~lucid1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `meican`
--

-- --------------------------------------------------------

--
-- Table structure for table `client_info`
--

DROP TABLE IF EXISTS `client_info`;
CREATE TABLE IF NOT EXISTS `client_info` (
  `cli_id` int(11) NOT NULL AUTO_INCREMENT,
  `alias` varchar(50) DEFAULT NULL,
  `ip_dcn` varchar(30) DEFAULT NULL,
  `ip_internet` varchar(30) DEFAULT NULL,
  `mac_address` varchar(60) DEFAULT NULL,
  `urn_id` int(11) NOT NULL,
  PRIMARY KEY (`cli_id`),
  UNIQUE KEY `alias` (`alias`,`ip_dcn`,`ip_internet`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;