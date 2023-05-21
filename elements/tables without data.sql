USE clopus_elements;

-- phpMyAdmin SQL Dump
-- version 4.0.10.14
-- http://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Generation Time: May 06, 2016 at 02:13 PM
-- Server version: 10.0.20-MariaDB-cll-lve
-- PHP Version: 5.4.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `clopus_elements`
--

-- --------------------------------------------------------

--
-- Table structure for table `allianceattacks`
--

CREATE TABLE IF NOT EXISTS `allianceattacks` (
  `attack_id` int(11) NOT NULL AUTO_INCREMENT,
  `attacker` int(10) unsigned NOT NULL,
  `defender` int(10) unsigned NOT NULL,
  `type` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `uncancelable` tinyint(1) NOT NULL,
  `sent` datetime NOT NULL,
  `ticks` int(10) unsigned NOT NULL,
  `resource_id` int(10) unsigned NOT NULL,
  `amount` int(10) unsigned NOT NULL,
  PRIMARY KEY (`attack_id`),
  KEY `attacker` (`attacker`),
  KEY `defender` (`defender`),
  KEY `resource_id` (`resource_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `alliancebankedresources`
--

CREATE TABLE IF NOT EXISTS `alliancebankedresources` (
  `alliance_id` int(10) unsigned NOT NULL,
  `resource_id` int(10) unsigned NOT NULL,
  `amount` int(10) unsigned NOT NULL,
  PRIMARY KEY (`alliance_id`,`resource_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `alliancedealitems_offered`
--

CREATE TABLE IF NOT EXISTS `alliancedealitems_offered` (
  `deal_id` int(10) unsigned NOT NULL,
  `resource_id` int(10) unsigned NOT NULL,
  `amount` int(11) NOT NULL,
  UNIQUE KEY `deal_id` (`deal_id`,`resource_id`),
  KEY `deal_id_2` (`deal_id`),
  KEY `resource_id` (`resource_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `alliancedealitems_requested`
--

CREATE TABLE IF NOT EXISTS `alliancedealitems_requested` (
  `deal_id` int(10) unsigned NOT NULL,
  `resource_id` int(10) unsigned NOT NULL,
  `amount` int(10) unsigned NOT NULL,
  UNIQUE KEY `deal_id` (`deal_id`,`resource_id`),
  KEY `deal_id_2` (`deal_id`),
  KEY `resource_id` (`resource_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `alliancedeals`
--

CREATE TABLE IF NOT EXISTS `alliancedeals` (
  `deal_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fromalliance` int(10) unsigned NOT NULL,
  `toalliance` int(10) unsigned NOT NULL,
  `finalized` tinyint(1) NOT NULL DEFAULT '0',
  `peaceturns` int(10) unsigned NOT NULL,
  PRIMARY KEY (`deal_id`),
  KEY `tonation` (`toalliance`),
  KEY `fromnation` (`fromalliance`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `allianceinvitations`
--

CREATE TABLE IF NOT EXISTS `allianceinvitations` (
  `alliance_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`alliance_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `alliancereports`
--

CREATE TABLE IF NOT EXISTS `alliancereports` (
  `time` datetime NOT NULL,
  `alliance_id` int(11) NOT NULL,
  `report` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  KEY `time` (`time`),
  KEY `user_id` (`alliance_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `allianceresources`
--

CREATE TABLE IF NOT EXISTS `allianceresources` (
  `alliance_id` int(10) unsigned NOT NULL,
  `resource_id` int(10) unsigned NOT NULL,
  `amount` int(10) unsigned NOT NULL,
  PRIMARY KEY (`alliance_id`,`resource_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `alliances`
--

CREATE TABLE IF NOT EXISTS `alliances` (
  `alliance_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `owner_id` int(10) unsigned NOT NULL,
  `alliancesatisfaction` int(10) unsigned NOT NULL,
  `alliancefocus` int(10) unsigned NOT NULL,
  `alliancefocusamount` int(10) unsigned NOT NULL,
  `name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`alliance_id`),
  KEY `owner_id` (`owner_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `alliance_groupabilities`
--

CREATE TABLE IF NOT EXISTS `alliance_groupabilities` (
  `alliance_id` int(11) unsigned NOT NULL,
  `ability_id` int(11) unsigned NOT NULL,
  `turns` int(10) unsigned NOT NULL,
  PRIMARY KEY (`alliance_id`,`ability_id`),
  KEY `user_id` (`alliance_id`),
  KEY `ability_id` (`ability_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `alliance_messages`
--

CREATE TABLE IF NOT EXISTS `alliance_messages` (
  `user_id` int(10) unsigned NOT NULL,
  `alliance_id` int(10) unsigned NOT NULL,
  `message` text COLLATE utf8_unicode_ci NOT NULL,
  `posted` datetime NOT NULL,
  `message_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`message_id`),
  KEY `user_id` (`user_id`),
  KEY `alliance_id` (`alliance_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `attacks`
--

CREATE TABLE IF NOT EXISTS `attacks` (
  `attack_id` int(11) NOT NULL AUTO_INCREMENT,
  `attacker` int(10) unsigned NOT NULL,
  `defender` int(10) unsigned NOT NULL,
  `type` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `uncancelable` tinyint(1) NOT NULL,
  `sent` datetime NOT NULL,
  `ticks` int(10) unsigned NOT NULL,
  `resource_id` int(10) unsigned NOT NULL,
  `amount` int(10) unsigned NOT NULL,
  PRIMARY KEY (`attack_id`),
  KEY `attacker` (`attacker`),
  KEY `defender` (`defender`),
  KEY `resource_id` (`resource_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `autocompounds`
--

CREATE TABLE IF NOT EXISTS `autocompounds` (
  `user_id` int(10) unsigned NOT NULL,
  `resource_id` int(10) unsigned NOT NULL,
  `amount` int(10) unsigned NOT NULL,
  UNIQUE KEY `user_id` (`user_id`,`resource_id`),
  KEY `user_id_2` (`user_id`),
  KEY `resource_id` (`resource_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bankedresources`
--

CREATE TABLE IF NOT EXISTS `bankedresources` (
  `resource_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `amount` int(10) unsigned NOT NULL,
  UNIQUE KEY `resource_id` (`resource_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `blocklist`
--

CREATE TABLE IF NOT EXISTS `blocklist` (
  `blocker` int(10) unsigned NOT NULL,
  `blockee` int(10) unsigned NOT NULL,
  UNIQUE KEY `blocker` (`blocker`,`blockee`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chat`
--

CREATE TABLE IF NOT EXISTS `chat` (
  `message` tinytext COLLATE utf8_unicode_ci NOT NULL,
  `posted` datetime NOT NULL,
  `user_id` int(11) NOT NULL,
  `message_id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`message_id`),
  KEY `user_id` (`user_id`),
  KEY `posted` (`posted`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `dealitems_offered`
--

CREATE TABLE IF NOT EXISTS `dealitems_offered` (
  `deal_id` int(10) unsigned NOT NULL,
  `resource_id` int(10) unsigned NOT NULL,
  `amount` int(11) NOT NULL,
  UNIQUE KEY `deal_id` (`deal_id`,`resource_id`),
  KEY `deal_id_2` (`deal_id`),
  KEY `resource_id` (`resource_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dealitems_requested`
--

CREATE TABLE IF NOT EXISTS `dealitems_requested` (
  `deal_id` int(10) unsigned NOT NULL,
  `resource_id` int(10) unsigned NOT NULL,
  `amount` int(10) unsigned NOT NULL,
  UNIQUE KEY `deal_id` (`deal_id`,`resource_id`),
  KEY `deal_id_2` (`deal_id`),
  KEY `resource_id` (`resource_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `deals`
--

CREATE TABLE IF NOT EXISTS `deals` (
  `deal_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fromuser` int(10) unsigned NOT NULL,
  `touser` int(10) unsigned NOT NULL,
  `finalized` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`deal_id`),
  KEY `tonation` (`touser`),
  KEY `fromnation` (`fromuser`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `kickattempts`
--

CREATE TABLE IF NOT EXISTS `kickattempts` (
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `logins`
--

CREATE TABLE IF NOT EXISTS `logins` (
  `user_id` int(10) unsigned NOT NULL,
  `ip` varchar(46) COLLATE utf8_unicode_ci NOT NULL,
  `forwarded` varchar(46) COLLATE utf8_unicode_ci NOT NULL,
  `forwarded_for` varchar(46) COLLATE utf8_unicode_ci NOT NULL,
  `logindate` datetime NOT NULL,
  `failed` tinyint(1) NOT NULL,
  KEY `user_id` (`user_id`,`ip`),
  KEY `ip` (`ip`),
  KEY `user_id_2` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `markasread`
--

CREATE TABLE IF NOT EXISTS `markasread` (
  `message_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `message_id` (`message_id`,`user_id`),
  KEY `message_id_2` (`message_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `marketplace`
--

CREATE TABLE IF NOT EXISTS `marketplace` (
  `marketplace_id` int(11) NOT NULL AUTO_INCREMENT,
  `offereditem` int(11) NOT NULL,
  `requesteditem` int(11) NOT NULL,
  `offeredamount` int(11) NOT NULL,
  `requestedamount` int(11) NOT NULL,
  `apparentitem` int(11) NOT NULL,
  `apparentamount` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `apparentuser_id` int(11) NOT NULL,
  `unmasked` tinyint(1) NOT NULL DEFAULT '0',
  `unmasker_id` int(11) NOT NULL,
  `multiplier` int(11) NOT NULL,
  `priority` int(11) NOT NULL,
  PRIMARY KEY (`marketplace_id`),
  KEY `user_id` (`user_id`),
  KEY `apparentuser_id` (`apparentuser_id`),
  KEY `offereditem` (`offereditem`),
  KEY `requesteditem` (`requesteditem`),
  KEY `apparentitem` (`apparentitem`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE IF NOT EXISTS `messages` (
  `message_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fromuser` int(10) unsigned NOT NULL,
  `touser` int(10) unsigned NOT NULL,
  `message` text COLLATE utf8_unicode_ci NOT NULL,
  `fromdeleted` tinyint(1) NOT NULL DEFAULT '0',
  `todeleted` tinyint(1) NOT NULL DEFAULT '0',
  `sent` datetime NOT NULL,
  `is_read` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`message_id`),
  KEY `fromuser` (`fromuser`),
  KEY `touser` (`touser`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE IF NOT EXISTS `news` (
  `message` text CHARACTER SET latin1 NOT NULL,
  `posted` datetime NOT NULL,
  `news_id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`news_id`),
  KEY `posted` (`posted`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `peacetreaties`
--

CREATE TABLE IF NOT EXISTS `peacetreaties` (
  `alliance1` int(11) NOT NULL,
  `alliance2` int(11) NOT NULL,
  `turns` int(11) NOT NULL,
  UNIQUE KEY `alliance1` (`alliance1`,`alliance2`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `philippy`
--

CREATE TABLE IF NOT EXISTS `philippy` (
  `philippy_id` int(11) NOT NULL AUTO_INCREMENT,
  `offereditem` int(11) NOT NULL,
  `offeredamount` int(11) NOT NULL,
  `maxpertick` int(10) unsigned NOT NULL,
  `maxtier` int(10) unsigned NOT NULL,
  `apparentitem` int(11) NOT NULL,
  `apparentamount` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `apparentuser_id` int(11) NOT NULL,
  `unmasked` tinyint(1) NOT NULL DEFAULT '0',
  `unmasker_id` int(11) NOT NULL,
  `bullshit` tinyint(1) NOT NULL,
  `priority` int(11) NOT NULL,
  PRIMARY KEY (`philippy_id`),
  KEY `user_id` (`user_id`),
  KEY `apparentuser_id` (`apparentuser_id`),
  KEY `offereditem` (`offereditem`),
  KEY `apparentitem` (`apparentitem`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `philippytaken`
--

CREATE TABLE IF NOT EXISTS `philippytaken` (
  `user_id` int(11) NOT NULL,
  `philippy_id` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  UNIQUE KEY `user_id` (`user_id`,`philippy_id`),
  KEY `user_id_2` (`user_id`),
  KEY `philippy_id` (`philippy_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `positionswaps`
--

CREATE TABLE IF NOT EXISTS `positionswaps` (
  `change_id` int(11) NOT NULL AUTO_INCREMENT,
  `changeposition1` int(11) NOT NULL,
  `changeposition2` int(11) NOT NULL,
  `effectivedate` datetime NOT NULL,
  PRIMARY KEY (`change_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE IF NOT EXISTS `reports` (
  `time` datetime NOT NULL,
  `user_id` int(11) NOT NULL,
  `report` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  KEY `time` (`time`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `resources`
--

CREATE TABLE IF NOT EXISTS `resources` (
  `resource_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `amount` int(10) unsigned NOT NULL,
  UNIQUE KEY `resource_id` (`resource_id`,`user_id`),
  KEY `resource_id_2` (`resource_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `topmessage`
--

CREATE TABLE IF NOT EXISTS `topmessage` (
  `message` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `alliance_id` int(10) unsigned NOT NULL,
  `creation_ip` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `donator` tinyint(1) NOT NULL,
  `stasisdate` datetime DEFAULT NULL,
  `stasismode` tinyint(1) NOT NULL DEFAULT '0',
  `css` smallint(6) NOT NULL DEFAULT '0',
  `hidebanners` tinyint(1) NOT NULL DEFAULT '0',
  `hideicons` tinyint(1) NOT NULL DEFAULT '0',
  `hidereports` tinyint(1) NOT NULL DEFAULT '0',
  `production` int(10) unsigned NOT NULL,
  `tier` int(10) unsigned NOT NULL,
  `satisfaction` int(10) unsigned NOT NULL,
  `ascended` tinyint(1) NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `focus` int(10) unsigned NOT NULL,
  `focusamount` int(10) unsigned NOT NULL,
  `alliance_messages_last_checked` datetime NOT NULL,
  PRIMARY KEY (`user_id`),
  KEY `alliance_id` (`alliance_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `user_abilities`
--

CREATE TABLE IF NOT EXISTS `user_abilities` (
  `user_id` int(11) unsigned NOT NULL,
  `ability_id` int(11) unsigned NOT NULL,
  `turns` int(10) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`ability_id`),
  KEY `user_id` (`user_id`),
  KEY `ability_id` (`ability_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;