/******
	This will create the Database and make a user for the API to read/write
	to the databse.
	
	On phpMyAdmin, go to "Import", select this file, and hit Go
	*******/

/* Creates the database */

Create database if not exists MagpieDB;

USE MagpieDB;


/* Creates the Tables */

CREATE TABLE if not exists Hunts(
  HID INTEGER PRIMARY KEY AUTO_INCREMENT,		-- Hunt ID
  UID VARCHAR(256),								-- This will be the User ID provided by Firebase
  Approved BIT DEFAULT NULL,					-- Whether it's approved by an Administrator (CMS Admin)
  HuntName VARCHAR(100) NOT NULL,				-- Name of the Hunt
  City VARCHAR(100) DEFAULT "Spokane",
  State VARCHAR(100) DEFAULT "Washington",
  ZipCode INTEGER DEFAULT 99207,
  Rating VARCHAR(100) DEFAULT "E",				-- Intended Audience
  Description VARCHAR(1000) NOT NULL, 
  Ordered BOOL DEFAULT FALSE,						-- Ordered
  Abbreviation VARCHAR(4) NOT NULL,
  Sponsor VARCHAR(50), 
  Email VARCHAR(255)							-- Email of the person who submitted the hunt
  
)ENGINE=InnoDB;


CREATE TABLE if not exists Badges(
  BID INTEGER PRIMARY KEY AUTO_INCREMENT,		-- Badge ID
  HID INTEGER NOT NULL,							-- Hunt ID that the Badge belongs to
  UID VARCHAR(256),								-- User ID supplied by Firebase, owner of the badge
  BadgeName VARCHAR(100) NOT NULL,				-- Name of Badge
  Latitude DOUBLE DEFAULT 0.0 NOT NULL,
  Longitude DOUBLE DEFAULT 0.0 NOT NULL,
  Description VARCHAR(1000) NOT NULL,
  QRCode VARCHAR(625) DEFAULT "{ EMPTY }",		-- QR Code as String
  PiHID INTEGER DEFAULT 0,
  BadgeID INTEGER DEFAULT 0,
  OrderNum INTEGER,
  qr_code blob,
  
  FOREIGN KEY (HID) REFERENCES Hunts(HID) ON DELETE CASCADE ON UPDATE CASCADE
  
)ENGINE=InnoDB;


CREATE TABLE if not exists Images(
  BID INTEGER,
  HID INTEGER,
  PiHID INTEGER AUTO_INCREMENT,		-- what is this
  
  PRIMARY KEY (PiHID),
  
  FOREIGN KEY (BID) REFERENCES Badges(BID) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (HID) REFERENCES Hunts(HID) ON DELETE CASCADE ON UPDATE CASCADE

)ENGINE=InnoDB;


CREATE TABLE if not exists Awards(
  AwardName VARCHAR(100),
  HID INTEGER PRIMARY KEY,				-- Reuses the associated Hunt ID as it's own primary key
  AwardDescription VARCHAR(1000),
  RedeemCode VARCHAR(255) UNIQUE,		-- unsure
  AwardValue varchar(255),
  
  FOREIGN KEY (HID) REFERENCES Hunts(HID) ON DELETE CASCADE ON UPDATE CASCADE
  
)ENGINE=InnoDB;

/* Can probably delete this table */
CREATE TABLE if not exists HuntImages(
  HID INTEGER,
  PiHID INTEGER AUTO_INCREMENT,
  
  PRIMARY KEY (PiHID),
  FOREIGN KEY (HID) REFERENCES Hunts(HID) ON DELETE CASCADE ON UPDATE CASCADE
  
)ENGINE=InnoDB;

/* Can probably delete this table */
CREATE TABLE if not exists HuntOwner(
  UID VARCHAR(10),
  HID INTEGER PRIMARY KEY,
  Email varchar(255),
  
  FOREIGN KEY (HID) REFERENCES Hunts(HID) ON DELETE CASCADE ON UPDATE CASCADE
  
)ENGINE=InnoDB;


CREATE TABLE IF NOT EXISTS Administrators(
	UID VARCHAR(255) PRIMARY KEY,
)ENGINE=InnoDB;

/* Creates the user for the API, 'magpieapi', so the API can work out-of-the-box with the
   config file.
   Note: This file is a security vulnerability, since it includes the password for the api user.
   However, the MySQL database should not be accessible via the network anyways. */

CREATE USER IF NOT EXISTS 'magpieapi'@'localhost' IDENTIFIED WITH mysql_native_password AS '#xs3Zh3(U^eZ5^UZ';

	/* GRANT SELECT, INSERT, UPDATE, DELETE, FILE ON `MagpieDB`.* TO 'magpieapi'@'localhost';  This line is for reference */

GRANT SELECT, INSERT, UPDATE, DELETE ON `MagpieDB`.* TO 'magpieapi'@'localhost';
