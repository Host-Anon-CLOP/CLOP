-- phpMyAdmin SQL Dump
-- version 4.0.10.14
-- http://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Generation Time: May 06, 2016 at 02:09 PM
-- Server version: 10.0.20-MariaDB-cll-lve
-- PHP Version: 5.4.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `clopus_clop`
--

-- --------------------------------------------------------

--
-- Table structure for table `alliances`
--

CREATE TABLE IF NOT EXISTS `alliances` (
  `alliance_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `public_description` text COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `owner_id` int(10) unsigned NOT NULL,
  `creationdate` datetime NOT NULL,
  PRIMARY KEY (`alliance_id`),
  KEY `owner_id` (`owner_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

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
-- Table structure for table `alliance_requests`
--

CREATE TABLE IF NOT EXISTS `alliance_requests` (
  `user_id` int(10) unsigned NOT NULL,
  `alliance_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `user_id_2` (`user_id`,`alliance_id`),
  KEY `alliance_id` (`alliance_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `armor`
--

CREATE TABLE IF NOT EXISTS `armor` (
  `nation_id` int(10) unsigned NOT NULL,
  `armor_id` int(10) unsigned NOT NULL,
  `amount` int(10) unsigned NOT NULL,
  UNIQUE KEY `armor_id_2` (`armor_id`,`nation_id`),
  KEY `nation_id` (`nation_id`),
  KEY `armor_id` (`armor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `armorbuyermarketplace`
--

CREATE TABLE IF NOT EXISTS `armorbuyermarketplace` (
  `nation_id` int(11) NOT NULL,
  `armor_id` int(11) NOT NULL,
  `amount` bigint(20) NOT NULL,
  `price` bigint(20) NOT NULL,
  UNIQUE KEY `nation_id_2` (`nation_id`,`armor_id`,`price`),
  KEY `nation_id` (`nation_id`),
  KEY `armor_id` (`armor_id`),
  KEY `price` (`price`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `armormarketplace`
--

CREATE TABLE IF NOT EXISTS `armormarketplace` (
  `nation_id` int(11) NOT NULL,
  `armor_id` int(10) NOT NULL,
  `amount` bigint(20) unsigned NOT NULL,
  `price` bigint(20) NOT NULL,
  UNIQUE KEY `nation_id_2` (`nation_id`,`armor_id`,`price`),
  KEY `nation_id` (`nation_id`),
  KEY `resource_id` (`armor_id`),
  KEY `amount` (`amount`),
  KEY `price` (`price`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ascendednations`
--

CREATE TABLE IF NOT EXISTS `ascendednations` (
  `name` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(11) NOT NULL,
  `date` datetime NOT NULL,
  KEY `user_id` (`user_id`),
  KEY `date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ascendedresources`
--

CREATE TABLE IF NOT EXISTS `ascendedresources` (
  `resource_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount` bigint(20) NOT NULL,
  UNIQUE KEY `resource_id_2` (`resource_id`,`user_id`),
  KEY `user_id` (`user_id`),
  KEY `resource_id` (`resource_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `banlist`
--

CREATE TABLE IF NOT EXISTS `banlist` (
  `ip` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `reason` varchar(256) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Multiple accounts',
  `date` datetime DEFAULT NULL,
  UNIQUE KEY `ip` (`ip`)
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
-- Table structure for table `buyermarketplace`
--

CREATE TABLE IF NOT EXISTS `buyermarketplace` (
  `nation_id` int(11) NOT NULL,
  `resource_id` int(11) NOT NULL,
  `amount` bigint(20) NOT NULL,
  `price` bigint(20) NOT NULL,
  UNIQUE KEY `nation_id_2` (`nation_id`,`resource_id`,`price`),
  KEY `nation_id` (`nation_id`),
  KEY `resource_id` (`resource_id`),
  KEY `price` (`price`)
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
-- Table structure for table `creation_banlist`
--

CREATE TABLE IF NOT EXISTS `creation_banlist` (
  `ip` varchar(45) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dealarmor_offered`
--

CREATE TABLE IF NOT EXISTS `dealarmor_offered` (
  `deal_id` int(10) unsigned NOT NULL,
  `armor_id` int(10) unsigned NOT NULL,
  `amount` int(11) NOT NULL,
  UNIQUE KEY `deal_id` (`deal_id`,`armor_id`),
  KEY `deal_id_2` (`deal_id`),
  KEY `resource_id` (`armor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dealarmor_requested`
--

CREATE TABLE IF NOT EXISTS `dealarmor_requested` (
  `deal_id` int(10) unsigned NOT NULL,
  `armor_id` int(10) unsigned NOT NULL,
  `amount` int(10) unsigned NOT NULL,
  UNIQUE KEY `deal_id` (`deal_id`,`armor_id`),
  KEY `deal_id_2` (`deal_id`),
  KEY `resource_id` (`armor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
  `fromnation` int(10) unsigned NOT NULL,
  `tonation` int(10) unsigned NOT NULL,
  `amount` bigint(20) unsigned NOT NULL,
  `askingformoney` tinyint(1) NOT NULL,
  `finalized` tinyint(1) NOT NULL,
  `paid` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`deal_id`),
  KEY `tonation` (`tonation`),
  KEY `fromnation` (`fromnation`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `dealweapons_offered`
--

CREATE TABLE IF NOT EXISTS `dealweapons_offered` (
  `deal_id` int(10) unsigned NOT NULL,
  `weapon_id` int(10) unsigned NOT NULL,
  `amount` int(11) NOT NULL,
  UNIQUE KEY `deal_id` (`deal_id`,`weapon_id`),
  KEY `deal_id_2` (`deal_id`),
  KEY `resource_id` (`weapon_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dealweapons_requested`
--

CREATE TABLE IF NOT EXISTS `dealweapons_requested` (
  `deal_id` int(10) unsigned NOT NULL,
  `weapon_id` int(10) unsigned NOT NULL,
  `amount` int(10) unsigned NOT NULL,
  UNIQUE KEY `deal_id` (`deal_id`,`weapon_id`),
  KEY `deal_id_2` (`deal_id`),
  KEY `resource_id` (`weapon_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `embargoes`
--

CREATE TABLE IF NOT EXISTS `embargoes` (
  `embargoer` int(10) unsigned NOT NULL,
  `embargoee` int(10) unsigned NOT NULL,
  KEY `embargoer` (`embargoer`),
  KEY `embargoee` (`embargoee`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `forcegroups`
--

CREATE TABLE IF NOT EXISTS `forcegroups` (
  `forcegroup_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `nation_id` int(11) NOT NULL,
  `location_id` int(11) NOT NULL,
  `destination_id` int(11) NOT NULL,
  `departuredate` datetime DEFAULT NULL,
  `attack_mission` tinyint(1) NOT NULL DEFAULT '0',
  `oldmission` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`forcegroup_id`),
  KEY `nation_id` (`nation_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `forces`
--

CREATE TABLE IF NOT EXISTS `forces` (
  `force_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `forcegroup_id` int(10) unsigned NOT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `nation_id` int(11) NOT NULL,
  `size` int(10) unsigned NOT NULL,
  `training` int(10) unsigned NOT NULL DEFAULT '0',
  `weapon_id` int(10) unsigned NOT NULL,
  `armor_id` int(10) unsigned NOT NULL,
  `type` int(10) unsigned NOT NULL,
  PRIMARY KEY (`force_id`),
  KEY `nation_id` (`nation_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `graveyard`
--

CREATE TABLE IF NOT EXISTS `graveyard` (
  `graveyard_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `details` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `deathdate` date NOT NULL,
  `killer` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`graveyard_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

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
-- Table structure for table `marketplace`
--

CREATE TABLE IF NOT EXISTS `marketplace` (
  `nation_id` int(11) NOT NULL,
  `resource_id` int(10) NOT NULL,
  `amount` bigint(20) unsigned NOT NULL,
  `price` bigint(20) NOT NULL,
  UNIQUE KEY `nation_id_2` (`nation_id`,`resource_id`,`price`),
  KEY `nation_id` (`nation_id`),
  KEY `resource_id` (`resource_id`),
  KEY `amount` (`amount`),
  KEY `price` (`price`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `nations`
--

CREATE TABLE IF NOT EXISTS `nations` (
  `nation_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `name` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `funds` bigint(20) NOT NULL DEFAULT '500000',
  `se_relation` int(11) NOT NULL DEFAULT '0',
  `nlr_relation` int(11) NOT NULL DEFAULT '0',
  `satisfaction` int(11) NOT NULL DEFAULT '0',
  `government` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Loose Despotism',
  `economy` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Poorly Defined',
  `region` int(4) NOT NULL,
  `creationdate` datetime NOT NULL,
  `active_economy` tinyint(1) NOT NULL DEFAULT '1',
  `gdp_last_turn` bigint(20) unsigned NOT NULL,
  `subregion` int(4) NOT NULL,
  `age` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`nation_id`),
  KEY `user_id` (`user_id`),
  KEY `gdp_last_turn` (`gdp_last_turn`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE IF NOT EXISTS `news` (
  `news_id` int(11) NOT NULL AUTO_INCREMENT,
  `message` text CHARACTER SET latin1 NOT NULL,
  `posted` datetime NOT NULL,
  PRIMARY KEY (`news_id`),
  KEY `posted` (`posted`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `recipefavorites`
--

CREATE TABLE IF NOT EXISTS `recipefavorites` (
  `nation_id` int(10) unsigned NOT NULL,
  `recipe_id` int(10) unsigned NOT NULL,
  `times` int(10) unsigned NOT NULL,
  UNIQUE KEY `nation_id` (`nation_id`,`recipe_id`,`times`),
  KEY `nation_id_2` (`nation_id`),
  KEY `times` (`times`),
  KEY `recipe_id` (`recipe_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE IF NOT EXISTS `reports` (
  `time` datetime NOT NULL,
  `nation_id` int(11) NOT NULL,
  `report` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  KEY `nation_id` (`nation_id`),
  KEY `time` (`time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `resources`
--

CREATE TABLE IF NOT EXISTS `resources` (
  `nation_id` int(10) unsigned NOT NULL,
  `resource_id` int(10) unsigned NOT NULL,
  `amount` bigint(20) unsigned NOT NULL,
  `disabled` int(10) unsigned NOT NULL,
  UNIQUE KEY `nation_type_unique` (`nation_id`,`resource_id`),
  KEY `nation_id` (`nation_id`),
  KEY `resource_id` (`resource_id`)
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
  `seesecrets` tinyint(1) NOT NULL DEFAULT '0',
  `empiremax` int(11) DEFAULT NULL,
  `hideicons` tinyint(1) NOT NULL DEFAULT '0',
  `hidereports` tinyint(1) NOT NULL DEFAULT '0',
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `lastactive` datetime DEFAULT NULL,
  `flag` tinytext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `weapons`
--

CREATE TABLE IF NOT EXISTS `weapons` (
  `nation_id` int(11) NOT NULL,
  `weapon_id` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  UNIQUE KEY `nation_id` (`nation_id`,`weapon_id`),
  KEY `nation_id_2` (`nation_id`),
  KEY `weapon_id` (`weapon_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `weaponsbuyermarketplace`
--

CREATE TABLE IF NOT EXISTS `weaponsbuyermarketplace` (
  `nation_id` int(11) NOT NULL,
  `weapon_id` int(11) NOT NULL,
  `amount` bigint(20) NOT NULL,
  `price` bigint(20) NOT NULL,
  UNIQUE KEY `nation_id_2` (`nation_id`,`weapon_id`,`price`),
  KEY `nation_id` (`nation_id`),
  KEY `weapon_id` (`weapon_id`),
  KEY `price` (`price`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `weaponsmarketplace`
--

CREATE TABLE IF NOT EXISTS `weaponsmarketplace` (
  `nation_id` int(11) NOT NULL,
  `weapon_id` int(10) NOT NULL,
  `amount` bigint(20) unsigned NOT NULL,
  `price` bigint(20) NOT NULL,
  UNIQUE KEY `nation_id_2` (`nation_id`,`weapon_id`,`price`),
  KEY `nation_id` (`nation_id`),
  KEY `resource_id` (`weapon_id`),
  KEY `amount` (`amount`),
  KEY `price` (`price`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
