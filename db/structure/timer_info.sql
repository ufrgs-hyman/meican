-- phpMyAdmin SQL Dump
-- version 3.3.2deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 16, 2011 at 06:04 PM
-- Server version: 5.1.41
-- PHP Version: 5.3.2-1ubuntu4.9

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
-- Table structure for table `timer_info`
--

CREATE TABLE IF NOT EXISTS `timer_info` (
  `tmr_id` int(11) NOT NULL AUTO_INCREMENT,
  `start` datetime NOT NULL,
  `finish` datetime NOT NULL,
  `freq` enum('DAILY','WEEKLY','MONTHLY') DEFAULT NULL,
  `until` datetime DEFAULT NULL,
  `count` int(11) DEFAULT NULL,
  `interval` int(11) DEFAULT NULL,
  `byday` set('SU','MO','TU','WE','TH','FR','SA') DEFAULT NULL,
  `summary` text,
  PRIMARY KEY (`tmr_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;