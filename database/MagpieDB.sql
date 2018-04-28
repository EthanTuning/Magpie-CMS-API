-- phpMyAdmin SQL Dump
-- version 4.4.15.7
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Mar 20, 2018 at 08:41 PM
-- Server version: 5.6.37
-- PHP Version: 5.6.31

/* Updated with some stuff to make a user */

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `MagpieDB`
--

CREATE DATABASE IF NOT EXISTS MagpieDB;
USE MagpieDB;

-- --------------------------------------------------------

--
-- Table structure for table `administrators`
--

CREATE TABLE IF NOT EXISTS `administrators` (
	`uid` varchar(255) PRIMARY KEY
) ENGINE=InnoDB;


--
-- Table structure for table `award`
--

CREATE TABLE IF NOT EXISTS `awards` (
  `award_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `address` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `lat` DOUBLE DEFAULT 0.0,
  `lon` DOUBLE DEFAULT 0.0,
  `name` varchar(255) DEFAULT NULL,
  `redeem_code` varchar(255) DEFAULT NULL,
  `award_value` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `badge`
--

CREATE TABLE IF NOT EXISTS `badges` (
  `badge_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `description` varchar(255) DEFAULT NULL,
  `icon` varchar(255) DEFAULT NULL,				/* URL for the badge icon so a client can D/L it */
  `image` varchar(255) DEFAULT NULL,			/* Also a URL */
  `landmark_name` varchar(255) DEFAULT NULL,
  `lat` DOUBLE DEFAULT 0.0,
  `lon` DOUBLE DEFAULT 0.0,
  `name` varchar(255) DEFAULT NULL,
  `qr_code` blob,
  `hunt_id` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `badge`
--

INSERT INTO `badges` (`badge_id`, `description`, `icon`, `image`, `landmark_name`, `lat`, `lon`, `name`, `qr_code`, `hunt_id`) VALUES
(1, 'This is badge number 1', 'Some Icon for badge 1', 'Some image for badge 1', 'EWU Campus', '47.4906', '-117.5855', 'EWU Badge', NULL, 123),
(2, 'Other Badge', 'Icon for Other Badge', 'Image for Other Badge', 'Moscow', '55.7558', '37.6173', 'Other Badge Moscow', NULL, 456);

-- --------------------------------------------------------

--
-- Table structure for table `creator`
--

CREATE TABLE IF NOT EXISTS `creators` (
  `uid` varchar(255) NOT NULL PRIMARY KEY,
  `email` varchar(255) DEFAULT NULL,
  `is_valid` bit(1) DEFAULT NULL,
  `level_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `creator`
--

INSERT INTO `creators` (`uid`, `email`, `is_valid`) VALUES
('pacmanw2', 'pacmanw2@mailinator.com', NULL),
('six_god', '6@god.com', NULL),
('someUser', 'some@thing.com', NULL),
('SYMuIyLoFkek6h0Vx8xy36T5aqK2', 'marcog3210@gmail.com', b'1');

-- --------------------------------------------------------

/*
-- Table structure for table `hunt_status`
-- This is a reference table for the status of a hunt to avoid using ENUM
*/

CREATE TABLE IF NOT EXISTS `hunt_status` (
	`approval_status` VARCHAR(32) PRIMARY KEY
) ENGINE=InnoDB;

-- Populate with appropriate statuses (KEY set manually to ensure consistency)

INSERT INTO `hunt_status` (`approval_status`) VALUES
	('non-approved'),
	('submitted'),
	('approved');

--
-- Table structure for table `hunt`
--

CREATE TABLE IF NOT EXISTS `hunts` (
  `hunt_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `abbreviation` varchar(255) DEFAULT NULL,
  `approval_status` VARCHAR(32) DEFAULT 'non-approved',
  `audience` varchar(255) DEFAULT NULL,
  `available` bit(1) DEFAULT NULL,
  `date_end` date DEFAULT NULL,
  `date_start` date DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `ordered` bit(1) DEFAULT NULL,
  `summary` varchar(255) DEFAULT NULL,
  `sponsor` VARCHAR(50),
  `super_badge` varchar(255) DEFAULT NULL,
  `uid` varchar(255) DEFAULT NULL,				/* uid from firebase, creator ID */
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(2) DEFAULT NULL,
  `zipcode` varchar(10) DEFAULT NULL,
  `award_id` int(11) DEFAULT NULL,
  
  FOREIGN KEY (`approval_status`) REFERENCES `hunt_status` (`approval_status`),
  FOREIGN KEY (`uid`) REFERENCES `creators` (`uid`)
  
) ENGINE=InnoDB AUTO_INCREMENT=667 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `hunt`
--

INSERT INTO `hunts` (`hunt_id`, `abbreviation`, `audience`, `available`, `date_end`, `date_start`, `name`, `ordered`, `summary`, `super_badge`, `uid`, `award_id`) VALUES
(123, 'rando_hunt', 'players', b'1', '2018-06-04', '2018-07-04', 'Random hunt', b'1', 'A random hunt that I made on the fly', 'A cool badge that has gold.', 'pacmanw2', NULL),
(456, 'other_hunt', 'people who play', b'1', '2018-02-01', '2018-02-02', 'Some Hunt', b'1', 'Another hunt I made up', 'Another badge I made up', 'someUser', NULL),
(666, 'six_hunt', 'Those who are running through the six with their woes', NULL, '2006-06-06', '2006-07-06', 'Six Hunt', b'1', 'A hunt through the six', 'A Six Badge', 'six_god', NULL);

-- --------------------------------------------------------

--
-- Indexes for table `badge`
--
ALTER TABLE `badges`
  ADD KEY `FKltwn4wel4vhkw4bfy5wh4m5gc` (`hunt_id`);

--
-- Indexes for table `hunt`
--
ALTER TABLE `hunts`
  ADD KEY `FKfwvuo3fy0wagttifb1gjedxhk` (`uid`),
  ADD KEY `FKoxl574w8ed3xt7dvu26usmn4r` (`award_id`);

-- Constraints for table `badges`
--
ALTER TABLE `badges`
  ADD CONSTRAINT `FKltwn4wel4vhkw4bfy5wh4m5gc` FOREIGN KEY (`hunt_id`) REFERENCES `hunts` (`hunt_id`);

--
-- Constraints for table `hunt`
--
ALTER TABLE `hunts`
  ADD CONSTRAINT `FKfwvuo3fy0wagttifb1gjedxhk` FOREIGN KEY (`uid`) REFERENCES `creators` (`uid`),
  ADD CONSTRAINT `FKoxl574w8ed3xt7dvu26usmn4r` FOREIGN KEY (`award_id`) REFERENCES `awards` (`award_id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;


/* Creates the user for the API, 'magpieapi', so the API can work out-of-the-box with the
   config file.
   Note: This file is a security vulnerability, since it includes the password for the api user.
   However, the MySQL database should not be accessible via the network anyways. */

CREATE USER IF NOT EXISTS 'magpieapi'@'localhost' IDENTIFIED BY 'eMO3B8cxFSo6usst';

/* This line is for reference: GRANT SELECT, INSERT, UPDATE, DELETE, FILE ON `MagpieDB`.* TO 'magpieapi'@'localhost';   */

GRANT SELECT, INSERT, UPDATE, DELETE ON `MagpieDB`.* TO 'magpieapi'@'localhost';

