/*********************************************************
	This will create the Database and make a user for the API to read/write
	to the databse.
	
	Using on local machine (development environment):

		1) Open phpMyAdmin
		2) Go to the "Import" tab, select this file, and hit Go

	Using on a live server (deployment environment):
	
		When using this script on bluehost, you'll need to use their MySQL tools to do steps 1 and 2:
		1) Create the database called `magpiehu_primary`
		2) Create a user `magpiehu_api` and give ONLY (SELECT, INSERT, UPDATE, DELETE) privilleges.
		
		Go to the phpMyAdmin from bluehost cpanel page (or whatever page links to it)
		3) Comment out the VERY bottom part of this script with the user stuff
		4) Follow the local machine steps above to Import into phpMyAdmin
	***************************************************************/

-- /*40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;  removed the ! before number
-- /*40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
-- /*40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;


SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


-- Database: `magpiehu_primary`
--

CREATE DATABASE IF NOT EXISTS magpiehu_primary;
USE magpiehu_primary;

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
('SYMuIyLoFkek6h0Vx8xy36T5aqK2', 'marcog3210@gmail.com', b'1'),
('zz2y1CPHhpYFZz2hFX0xGirs3iR2', 'wolfofhalo@gmail.com', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `administrators`
--

CREATE TABLE IF NOT EXISTS `administrators` (
	`uid` varchar(255) PRIMARY KEY,

	FOREIGN KEY (`uid`) REFERENCES `creators` (`uid`)

) ENGINE=InnoDB;

-- Make Marco and Rem Administrators

INSERT INTO `administrators` (`uid`) VALUES
('SYMuIyLoFkek6h0Vx8xy36T5aqK2'),
('zz2y1CPHhpYFZz2hFX0xGirs3iR2');

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
  `date_end` date DEFAULT NULL,
  `date_start` date DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `ordered` BOOL DEFAULT FALSE,
  `summary` varchar(255) DEFAULT NULL,
  `sponsor` VARCHAR(50),
  `super_badge` varchar(255) DEFAULT NULL,
  `uid` varchar(255) DEFAULT NULL,				/* uid from firebase, creator ID */
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(2) DEFAULT NULL,
  `zipcode` varchar(10) DEFAULT NULL,
  
  FOREIGN KEY (`approval_status`) REFERENCES `hunt_status` (`approval_status`),
  FOREIGN KEY (`uid`) REFERENCES `creators` (`uid`)
  
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


--
-- Dumping data for table `hunt`
--

INSERT INTO `hunts` (`hunt_id`, `abbreviation`, `audience`, `date_end`, `date_start`, `name`, `ordered`, `summary`, `super_badge`, `uid`) VALUES
(123, 'rando_hunt', 'players', '2018-06-04', '2018-07-04', 'Random hunt', b'1', 'A random hunt that I made on the fly', 'A cool badge that has gold.', 'pacmanw2'),
(456, 'other_hunt', 'people who play', '2018-02-01', '2018-02-02', 'Some Hunt', b'1', 'Another hunt I made up', 'Another badge I made up', 'someUser'),
(666, 'six_hunt', 'Those who are running through the six with their woes', '2006-06-06', '2006-07-06', 'Six Hunt', b'1', 'A hunt through the six', 'A Six Badge', 'SYMuIyLoFkek6h0Vx8xy36T5aqK2');

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
  `hunt_id` int(11) DEFAULT NULL,

	FOREIGN KEY (`hunt_id`) REFERENCES `hunts` (`hunt_id`) ON DELETE CASCADE

) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `badge`
--

INSERT INTO `badges` (`badge_id`, `description`, `icon`, `image`, `landmark_name`, `lat`, `lon`, `name`, `qr_code`, `hunt_id`) VALUES
(1, 'This is badge number 1', 'Some Icon for badge 1', 'Some image for badge 1', 'EWU Campus', '47.4906', '-117.5855', 'EWU Badge', NULL, 123),
(2, 'Other Badge', 'Icon for Other Badge', 'Image for Other Badge', 'Moscow', '55.7558', '37.6173', 'Other Badge Moscow', NULL, 456);

-- --------------------------------------------------------

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
  `award_value` varchar(255) DEFAULT NULL,
  `hunt_id` int	DEFAULT NULL,

   FOREIGN KEY (`hunt_id`) REFERENCES `hunts` (`hunt_id`) ON DELETE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=latin1;



/* Creates the user for the API, 'magpieapi', so the API can work out-of-the-box with the
   config file.

   Note: This file is a security vulnerability, since it includes the password for the api user.
   However, the MySQL database should not be accessible via the network anyways. */

CREATE USER IF NOT EXISTS 'magpiehu_api'@'localhost' IDENTIFIED BY 'eMO3B8cxFSo6usst';

/* This line is for reference: GRANT SELECT, INSERT, UPDATE, DELETE, FILE ON `MagpieDB`.* TO 'magpieapi'@'localhost';   */

GRANT SELECT, INSERT, UPDATE, DELETE ON `magpiehu_primary`.* TO 'magpiehu_api'@'localhost';


