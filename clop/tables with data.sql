-- phpMyAdmin SQL Dump
-- version 4.0.10.14
-- http://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Generation Time: May 06, 2016 at 02:11 PM
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
-- Table structure for table `armordefs`
--

CREATE TABLE IF NOT EXISTS `armordefs` (
  `armor_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `type` int(10) unsigned NOT NULL,
  `arm_cavalry` decimal(5,2) NOT NULL,
  `arm_tanks` decimal(5,2) NOT NULL,
  `arm_pegasi` decimal(5,2) NOT NULL,
  `arm_unicorns` decimal(5,2) NOT NULL,
  `arm_naval` decimal(5,2) NOT NULL,
  `carrying` int(10) unsigned NOT NULL,
  PRIMARY KEY (`armor_id`),
  KEY `type` (`type`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=19 ;

--
-- Dumping data for table `armordefs`
--

INSERT INTO `armordefs` (`armor_id`, `name`, `type`, `arm_cavalry`, `arm_tanks`, `arm_pegasi`, `arm_unicorns`, `arm_naval`, `carrying`) VALUES
(1, 'Barding', 1, '0.40', '0.80', '0.80', '0.80', '0.80', 0),
(2, 'Bigdog', 1, '0.65', '0.40', '0.65', '0.65', '0.65', 0),
(3, 'Nope', 1, '0.50', '0.50', '0.50', '0.50', '0.50', 0),
(4, 'Trundle', 2, '0.30', '0.65', '0.65', '0.65', '0.65', 0),
(5, 'Shepherd', 2, '0.55', '0.55', '0.55', '0.55', '0.35', 0),
(6, 'Ohno', 2, '0.45', '0.35', '0.45', '0.45', '0.45', 0),
(7, 'Titan', 2, '0.35', '0.40', '0.40', '0.30', '0.40', 0),
(8, 'Cooler', 3, '0.80', '0.80', '0.35', '0.80', '0.80', 0),
(9, 'Wonder', 3, '0.30', '0.70', '0.70', '0.70', '0.70', 0),
(10, 'Griffin', 3, '0.45', '0.45', '0.45', '0.45', '0.40', 2),
(11, 'Dragon', 3, '0.35', '0.35', '0.45', '0.45', '0.45', 4),
(12, 'Hornshield', 4, '0.30', '0.65', '0.65', '0.65', '0.65', 0),
(13, 'Librarian', 4, '0.55', '0.55', '0.55', '0.30', '0.55', 0),
(14, 'Shining', 4, '0.45', '0.45', '0.45', '0.45', '0.35', 0),
(15, 'D2A', 4, '0.35', '0.40', '0.35', '0.40', '0.40', 0),
(16, 'C-PON3', 5, '0.65', '0.65', '0.65', '0.65', '0.30', 2),
(17, 'Esohes', 5, '0.35', '0.35', '0.55', '0.55', '0.55', 4),
(18, 'Shubidu', 5, '0.40', '0.40', '0.40', '0.40', '0.40', 8);

-- --------------------------------------------------------

--
-- Table structure for table `armorrecipeitems`
--

CREATE TABLE IF NOT EXISTS `armorrecipeitems` (
  `armorrecipe_id` int(11) NOT NULL,
  `resource_id` int(11) unsigned NOT NULL,
  `is_used_up` tinyint(1) NOT NULL,
  `amount` int(10) unsigned NOT NULL,
  UNIQUE KEY `armorrecipe_id_2` (`armorrecipe_id`,`resource_id`),
  KEY `resource_id` (`resource_id`),
  KEY `armorrecipe_id` (`armorrecipe_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `armorrecipeitems`
--

INSERT INTO `armorrecipeitems` (`armorrecipe_id`, `resource_id`, `is_used_up`, `amount`) VALUES
(1, 2, 1, 2),
(2, 2, 1, 4),
(2, 5, 0, 1),
(2, 27, 1, 4),
(3, 2, 1, 8),
(3, 30, 1, 1),
(3, 31, 0, 1),
(4, 2, 1, 5),
(4, 5, 0, 1),
(4, 9, 1, 1),
(5, 2, 1, 10),
(5, 5, 0, 1),
(5, 9, 1, 1),
(5, 30, 1, 1),
(6, 2, 1, 15),
(6, 5, 0, 1),
(6, 9, 1, 1),
(6, 28, 1, 1),
(6, 30, 1, 1),
(7, 2, 1, 20),
(7, 9, 1, 1),
(7, 30, 1, 2),
(7, 31, 0, 1),
(8, 30, 1, 2),
(9, 5, 0, 1),
(9, 30, 1, 4),
(10, 5, 0, 1),
(10, 30, 1, 6),
(11, 30, 1, 8),
(11, 31, 0, 1),
(12, 5, 0, 1),
(12, 29, 1, 1),
(12, 30, 1, 2),
(13, 5, 0, 1),
(13, 29, 1, 1),
(13, 30, 1, 4),
(14, 29, 1, 2),
(14, 30, 1, 5),
(14, 31, 0, 1),
(15, 29, 1, 3),
(15, 30, 1, 6),
(15, 31, 0, 1),
(16, 2, 1, 10),
(16, 9, 1, 2),
(16, 30, 1, 1),
(17, 2, 1, 15),
(17, 9, 1, 2),
(17, 30, 1, 2),
(18, 2, 1, 20),
(18, 9, 1, 2),
(18, 30, 1, 4);

-- --------------------------------------------------------

--
-- Table structure for table `armorrecipes`
--

CREATE TABLE IF NOT EXISTS `armorrecipes` (
  `armorrecipe_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `armor_id` int(10) unsigned NOT NULL,
  `amount` int(10) unsigned NOT NULL,
  `cost` int(10) unsigned NOT NULL,
  PRIMARY KEY (`armorrecipe_id`),
  KEY `armor_id` (`armor_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=19 ;

--
-- Dumping data for table `armorrecipes`
--

INSERT INTO `armorrecipes` (`armorrecipe_id`, `name`, `description`, `armor_id`, `amount`, `cost`) VALUES
(1, 'Build Barding', '', 1, 1, 20000),
(2, 'Build Bigdog', '', 2, 1, 30000),
(3, 'Build Nope', '', 3, 1, 40000),
(4, 'Build Trundle', '', 4, 1, 30000),
(5, 'Build Shepherd', '', 5, 1, 40000),
(6, 'Build Ohno', '', 6, 1, 50000),
(7, 'Build Titan', '', 7, 1, 70000),
(8, 'Build Cooler', '', 8, 1, 30000),
(9, 'Build Wonder', '', 9, 1, 40000),
(10, 'Build Griffin', '', 10, 1, 60000),
(11, 'Build Dragon', '', 11, 1, 80000),
(12, 'Build Hornshield', '', 12, 1, 35000),
(13, 'Build Librarian', '', 13, 1, 50000),
(14, 'Build Shining', '', 14, 1, 70000),
(15, 'Build D2A', '', 15, 1, 80000),
(16, 'Build C-PON3', '', 16, 1, 40000),
(17, 'Build Esohes', '', 17, 1, 70000),
(18, 'Build Shubidu', '', 18, 1, 100000);

-- --------------------------------------------------------

--
-- Table structure for table `recipegroups`
--

CREATE TABLE IF NOT EXISTS `recipegroups` (
  `recipegroup_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`recipegroup_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=10 ;

--
-- Dumping data for table `recipegroups`
--

INSERT INTO `recipegroups` (`recipegroup_id`, `name`) VALUES
(1, 'Basic Actions'),
(2, 'Resource Extraction'),
(3, 'Resource Conversion'),
(4, 'Satisfaction Buildings'),
(5, 'Factories'),
(6, 'Manufacturing'),
(7, 'Military'),
(8, 'Superpower Relations'),
(9, 'Special');

-- --------------------------------------------------------

--
-- Table structure for table `recipeitems`
--

CREATE TABLE IF NOT EXISTS `recipeitems` (
  `recipe_id` int(11) NOT NULL,
  `resource_id` int(11) unsigned NOT NULL,
  `is_used_up` tinyint(1) NOT NULL,
  `amount` int(10) unsigned NOT NULL,
  KEY `resource_id` (`resource_id`),
  KEY `recipe_id` (`recipe_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `recipeitems`
--

INSERT INTO `recipeitems` (`recipe_id`, `resource_id`, `is_used_up`, `amount`) VALUES
(4, 1, 1, 5),
(5, 4, 1, 12),
(5, 2, 1, 30),
(6, 2, 1, 1),
(6, 5, 0, 1),
(8, 3, 1, 5),
(7, 2, 1, 1),
(7, 5, 0, 1),
(9, 13, 1, 1),
(10, 6, 1, 1),
(10, 2, 1, 5),
(10, 9, 1, 7),
(10, 10, 1, 15),
(11, 7, 1, 1),
(11, 2, 1, 20),
(11, 9, 1, 5),
(11, 10, 1, 10),
(12, 8, 1, 1),
(12, 9, 1, 20),
(12, 10, 1, 5),
(6, 4, 1, 1),
(7, 4, 1, 1),
(13, 2, 1, 5),
(13, 9, 1, 5),
(13, 10, 1, 5),
(14, 2, 1, 10),
(14, 10, 1, 20),
(13, 4, 1, 5),
(16, 2, 1, 15),
(16, 9, 1, 5),
(16, 10, 1, 5),
(17, 9, 1, 10),
(17, 10, 1, 5),
(18, 2, 1, 40),
(19, 2, 1, 40),
(20, 2, 1, 75),
(20, 4, 1, 50),
(20, 10, 1, 20),
(20, 9, 1, 10),
(21, 11, 1, 1),
(21, 2, 1, 30),
(21, 10, 1, 10),
(22, 2, 1, 40),
(22, 10, 1, 15),
(23, 29, 1, 3),
(23, 9, 1, 10),
(23, 10, 1, 10),
(24, 4, 1, 50),
(24, 2, 1, 50),
(24, 9, 1, 20),
(24, 10, 1, 20),
(25, 30, 1, 5),
(25, 2, 1, 75),
(25, 9, 1, 15),
(25, 10, 1, 10),
(26, 31, 0, 1),
(26, 2, 1, 1),
(26, 26, 1, 1),
(27, 31, 0, 1),
(27, 27, 1, 1),
(27, 28, 1, 1),
(28, 9, 1, 15),
(28, 10, 1, 20),
(28, 29, 1, 5),
(29, 2, 1, 500),
(29, 4, 1, 500),
(29, 9, 1, 50),
(29, 10, 1, 50),
(30, 4, 1, 50),
(30, 2, 1, 50),
(31, 2, 1, 20000),
(32, 1, 1, 8),
(33, 1, 1, 8),
(34, 2, 1, 50),
(34, 28, 1, 20),
(35, 2, 1, 50),
(35, 28, 1, 20),
(37, 42, 1, 1),
(38, 42, 1, 1),
(39, 2, 1, 30),
(39, 30, 1, 10),
(40, 2, 1, 500),
(41, 2, 1, 500),
(40, 10, 1, 100),
(41, 10, 1, 100),
(40, 30, 1, 100),
(41, 30, 1, 100),
(42, 2, 1, 100),
(42, 10, 1, 5),
(42, 29, 1, 5),
(43, 2, 1, 50),
(44, 2, 1, 200),
(44, 9, 1, 10),
(44, 10, 1, 10),
(44, 27, 1, 10),
(57, 2, 1, 1000),
(57, 10, 1, 300),
(57, 29, 1, 300),
(61, 34, 1, 1),
(61, 10, 1, 20),
(61, 29, 1, 5),
(61, 30, 1, 5),
(62, 27, 1, 8),
(63, 27, 1, 8);

-- --------------------------------------------------------

--
-- Table structure for table `recipes`
--

CREATE TABLE IF NOT EXISTS `recipes` (
  `recipe_id` int(11) NOT NULL AUTO_INCREMENT,
  `region` int(4) NOT NULL,
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `resource_id` int(10) unsigned NOT NULL,
  `amount` int(10) unsigned NOT NULL,
  `cost` int(11) NOT NULL,
  `cost_przewalskia` int(11) NOT NULL,
  `se_relation` int(11) NOT NULL,
  `nlr_relation` int(11) NOT NULL,
  `satisfaction` int(11) NOT NULL,
  `secret` tinyint(1) NOT NULL,
  `recipegroup_id` int(11) NOT NULL,
  `subregion` int(4) NOT NULL,
  PRIMARY KEY (`recipe_id`),
  KEY `resource_id` (`resource_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=64 ;

--
-- Dumping data for table `recipes`
--

INSERT INTO `recipes` (`recipe_id`, `region`, `name`, `description`, `resource_id`, `amount`, `cost`, `cost_przewalskia`, `se_relation`, `nlr_relation`, `satisfaction`, `secret`, `recipegroup_id`, `subregion`) VALUES
(1, 1, 'Build Basic Oil Well', 'Tap into the thick, black crude made before the time of Eohippus. This primitive oil well is a dirty place to work (-2 sat/tick), but it provides the lifeblood of the world (5 oil/tick). Building more than 10 hurts the environment.', 6, 1, 200000, 0, 0, 0, 0, 0, 2, 0),
(2, 2, 'Dig Basic Copper Mine', 'Your zebras will toil underground in the dark with no mechanical help (-2 sat) to get 5 copper every tick. Building more than 10 hurts the environment.', 7, 1, 200000, 0, 0, 0, 0, 0, 2, 0),
(3, 3, 'Plow Basic Sugar Farm', 'Plow a basic sugar-growing farm that uses only horsepower and no modern equipment for -2 sat and 5 sugar a tick. Building more than 10 hurts the environment.', 8, 1, 200000, 0, 0, 0, 0, 0, 2, 0),
(4, 0, 'Burn Oil', 'Burn 5 units of oil in inefficient, hand-me-down generators in a wasteful process that severely pollutes the air (-5 sat) and only yields 5 units of energy.', 4, 5, 0, 0, 0, 0, -5, 0, 1, 0),
(5, 0, 'Build Basic Factory', 'Using 12 energy and 30 copper, build a basic factory that turns 1 unit of energy into 10k money, depending on your economy type, every tick. It''s not the greatest place to work (-1 sat/tick), but owning this factory lets you make other things with it. If you build more than 20, overindustrialization starts hurting the environment.', 5, 1, 500000, 150000, 0, 0, 0, 0, 5, 0),
(6, 0, 'Build Vehicle Parts', 'Have your factory workers turn 1 energy and 1 copper into 1 box of vehicle parts suitable for export.', 9, 1, 20000, 200, 0, 0, 0, 0, 6, 0),
(7, 0, 'Build Machinery', 'Have your factory workers use 1 energy and 1 copper to create 1 box of mechanical parts useful in the construction of more advanced buildings.', 10, 1, 30000, 300, 0, 0, 0, 0, 6, 0),
(8, 0, 'Distribute Sugar', 'Distributing sugar to your little ponies is highly inefficient, but at least it gets the job done. Distribute 5 sugar for 3 satisfaction.', 0, 0, 0, 0, 0, 0, 3, 0, 1, 0),
(9, 0, 'Distribute Cupcakes', 'Distribute 1 large box of cupcakes to your little ponies to raise their satisfaction by 2.', 0, 0, 0, 0, 0, 0, 2, 0, 1, 0),
(10, 1, 'Upgrade Oil Well', 'With 5 copper, 7 vehicle parts, and 15 machinery parts, you make an oil well an easier and more efficient place to work at the cost of regular energy use. (+5 immediate sat; -1 sat, -1 energy, 8 oil per tick) Building more than 20 starts wreaking havoc with the environment.', 14, 1, 200000, 200000, 0, 0, 5, 0, 2, 0),
(11, 2, 'Upgrade Copper Mine', 'With 20 copper, 5 vehicle parts, and 10 machinery parts, you make your copper mine an easier and more efficient place to work at the cost of regular energy use. (+5 immediate sat; -1 sat, -1 energy, 8 copper per tick) Building more than 20 starts wreaking havoc with the environment.', 15, 1, 200000, 200000, 0, 0, 5, 0, 2, 0),
(12, 3, 'Upgrade Sugar Farm', 'With 20 vehicle parts and 5 machinery parts, you make your sugar farm an easier and more efficient place to work at the cost of regular energy use. (+5 immediate sat; -1 sat, -1 energy, 8 sugar per tick) Building more than 20 starts wreaking havoc with the environment.', 16, 1, 200000, 200000, 0, 0, 5, 0, 2, 0),
(13, 0, 'Build Bakery', 'Far more efficient than distributing sugar directly, the bakery uses 1 energy and 2 sugar to make 3 boxes of cupcakes and add 1 satisfaction every tick. Ponies become more satisfied by 2 just watching it be built. Needs 5 copper, 5 energy, 5 vehicle parts, and 5 machinery parts to build.', 12, 1, 400000, 200000, 0, 0, 2, 0, 4, 0),
(14, 0, 'Build Oil Combustion Facility', 'For 10 copper and 20 machinery parts, you build the Oil Combustion Facility, which turns 8 oil into 12 energy every tick at the cost of 2 satisfaction. Building more than 5 starts creating air pollution.', 11, 1, 500000, 250000, 0, 0, 0, 0, 3, 0),
(15, 0, 'Distribute Money', 'Spend 100,000 bits to get a measly 1 satisfaction. This is probably the worst use of money possible and won''t work in the long run, but if you''re desperately trying to stave off losing your country...', 0, 0, 100000, 100000, 0, 0, 1, 0, 1, 0),
(16, 0, 'Build Vodka Production Facility', 'With 1 energy and 5 sugar a tick, you ferment 5 vodka! Serve it in bars and fuel your state economy with it. Requires 15 copper, 5 vehicle parts, and 5 machinery parts to build. Does nothing for satisfaction one way or another, as the ponies working there are high on their own supply.', 17, 1, 300000, 300000, 0, 0, 0, 0, 3, 0),
(17, 3, 'Plow Coffee Farm', 'As ponies have no hands, mechanical help is absolutely necessary to pick and transport these beans. For use in coffee shops and in free markets. Requires 10 vehicle parts and 5 machinery parts to build. Grants 5 coffee per tick. Uses 1 energy and costs 1 satisfaction per tick due to its difficulty. Building more than 15 causes environmental damage.', 19, 1, 200000, 200000, 0, 0, 0, 0, 2, 0),
(18, 0, 'Build Coffee Shop', 'Use 40 copper to construct a coffee shop that uses up 3 coffee and 1 cupcakes a tick to increase your ponies'' satisfaction by 10. Raises satisfaction by 5 just being built.', 21, 1, 150000, 150000, 0, 0, 5, 0, 4, 0),
(19, 0, 'Build Bar', 'Use 40 copper to construct a bar that uses up 3 vodka and 1 cupcakes a tick to increase your ponies'' satisfaction by 10. Raises satisfaction by 5 just being built.', 22, 1, 150000, 150000, 0, 0, 5, 0, 4, 0),
(20, 0, 'Build Gasoline Refinery', 'Every tick, you refine 10 oil into 8 gasoline. Costs 75 copper, 50 energy, 20 machinery parts, and 10 vehicle parts to build. No per-tick satisfaction loss, as the refining process takes away pollutants that would otherwise escape into the air.', 23, 1, 300000, 200000, 0, 0, 0, 0, 3, 0),
(21, 0, 'Upgrade to Gasoline Combustion Facility', 'With 30 copper and 10 machinery parts, you transform one of your oil burning facilities into one that burns gasoline at the rate of 8 gasoline for 12 energy. You gain 5 sat for making the change. Gasoline burns much more cleanly than oil, so you lose no sat per tick from its use.', 24, 1, 200000, 100000, 0, 0, 5, 0, 3, 0),
(22, 2, 'Dig Gem Mine', 'For 300,000 bits, 40 copper, and 15 machinery parts, you dig a gem mine that creates 3 gems per tick. However, the ponies need coffee to turn their heads away from all the glitter. Uses up 1 coffee and 1 energy per tick. Costs 1 satisfaction per tick. Build more than 7, and environmental damage occurs.', 32, 1, 300000, 300000, 0, 0, 0, 0, 2, 0),
(23, 2, 'Dig Tungsten Mine', 'For 300,000 bits, 3 precision parts, 10 vehicle parts, and 10 machinery parts, you dig a tungsten mine that creates 4 tungsten per tick. However, the poisons in the mine can only be neutralized with alcohol. Uses up 1 vodka and 1 energy per tick. Costs 1 satisfaction per turn. Build more than 7, and environmental damage occurs.', 33, 1, 300000, 300000, 0, 0, 0, 0, 2, 0),
(24, 0, 'Build Advanced Factory', 'For 50 copper, 50 energy, 20 machinery parts, and 20 vehicle parts, you make a factory that uses 2 energy per tick (and -1 satisfaction a tick) to generate 20k GDP and allows for the creation of precision parts and composites. Build more than 5 and the environmentalists get on your ass. However, it''s a large enough achievement that you get 20 satisfaction for building it.', 31, 1, 750000, 500000, 0, 0, 20, 0, 5, 0),
(25, 1, 'Build Oil Fracker', 'The oil fracker is a tremendous machine that reaches deep into the earth to get 20 oil per tick. Costs 75 copper, 15 vehicle parts, 10 machinery parts, and 5 composites to build. Uses up 2 energy per tick and costs 2 satisfaction a tick. Building more than 5 makes your ponies go up in forelegs about fracking.', 35, 1, 500000, 500000, 0, 0, 0, 0, 2, 0),
(26, 0, 'Manufacture Precision Parts', 'Using 1 gem and 1 copper, you manufacture 1 precision parts useful in the construction of tungsten mines and certain weapons.', 29, 1, 40000, 400, 0, 0, 0, 0, 6, 0),
(27, 0, 'Manufacture Composites', 'Using 1 tungsten and 1 plastics, you manufacture 1 composites useful in the construction of oil frackers and certain armor.', 30, 1, 40000, 400, 0, 0, 0, 0, 6, 0),
(28, 0, 'Build Plastics Factory', 'For 1 energy and -1 satisfaction a tick, you turn 4 oil into 4 plastics. Requires 5 precision parts, 15 vehicle parts, and 20 machinery parts to build. Build more than 7 and the fumes become an environmental problem.', 34, 1, 200000, 200000, 0, 0, 0, 0, 3, 0),
(29, 0, 'Build Barracks', 'For 4 million bits, 500 copper, 500 energy, 50 vehicle parts, and 50 machinery parts, you build a Barracks that increases your new forces'' training by 1. More Barracks stack. However, building more than 1 Barracks causes environmental devastation- and you can''t disable them.', 36, 1, 4000000, 4000000, 0, 0, 0, 0, 7, 0),
(30, 0, 'Build Video Arcade', 'For 50 copper and 50 energy, you build a video arcade that uses 5 energy and 1 cupcakes a tick to yield 10 satisfaction. Gives 5 satisfaction just being built.', 37, 1, 500000, 500000, 0, 0, 5, 0, 4, 0),
(31, 0, 'Build Statue', 'Use 20,000 copper to build a huge statue to inspire your ponies and provide 1 satisfaction a tick. Requires no per-tick inputs. Gives 50 satisfaction being built.', 38, 1, 5000000, 5000000, 0, 0, 50, 0, 4, 0),
(32, 0, 'Ship Oil to the Solar Empire', 'Send 8 oil to the Solar Empire to better your relationship with it by 1.', 0, 0, 0, 0, 1, 0, 0, 0, 8, 0),
(33, 0, 'Ship Oil to the New Lunar Republic', 'Send 8 oil to the New Lunar Republic to better your relationship with it by 1.', 0, 0, 0, 0, 0, 1, 0, 0, 8, 0),
(34, 0, 'Build Sun Worship Center', 'For 50 copper and 20 plastics, you build a sun worship center that uses 2 coffee and 6 energy to raise your relationship with the Solar Empire by 1 every tick.', 39, 1, 300000, 300000, 0, 0, 0, 0, 8, 0),
(35, 0, 'Build Moon Worship Center', 'For 50 copper and 20 plastics, you build a moon worship center that uses 2 vodka and 6 energy to raise your relationship with the New Lunar Republic by 1 every tick.', 40, 1, 300000, 300000, 0, 0, 0, 0, 8, 0),
(36, 3, 'Drug Farm', 'For 200,000 bits and no resources, you plant a drug farm. It produces 10 drugs a tick that can be sold to the Solar Empire or New Lunar Republic for 20,000 bits each and a relationship hit of 1 with the nation that you sell them to. While it has no effect on satisfaction, its mere existence reduces your relationship with both the Solar Empire and New Lunar Republic by 1 a tick.', 41, 1, 200000, 200000, 0, 0, 0, 0, 2, 0),
(37, 0, 'Smuggle Drugs into the SE', 'Smuggle drugs into the Solar Empire to gain 20,000 bits. You will lose 1 relationship with the Solar Empire for doing this (but that might be why you did...) You cannot do this if your relationship with the Solar Empire is below -500 or if your nation is younger than a week.', 0, 0, -20000, -20000, -1, 0, 0, 0, 8, 0),
(38, 0, 'Smuggle Drugs into the NLR', 'Smuggle drugs into the New Lunar Republic to gain 20,000 bits. You will lose 1 relationship with the New Lunar Republic for doing this (but that might be why you did...) You cannot do this if your relationship with the New Lunar Republic is below -500 or if your nation is younger than a week.', 0, 0, -20000, -20000, 0, -1, 0, 0, 8, 0),
(40, 0, 'Build Solar Environmental Facility', 'Through advanced technology and solar magic, you use 500 copper, 100 machinery parts, and 100 composites to build a facility that reduces all environmental damage by 10% at the cost of 5 energy a tick. You must have at least 900 relationship with the Solar Empire to build this. You can only build 5 of these. Having 2 negates 19% environmental damage, not 20%.', 44, 1, 20000000, 20000000, 0, 0, 0, 0, 4, 0),
(41, 0, 'Build Lunar Environmental Facility', 'Through advanced technology and the help of NLR unicorns, you use 500 copper, 100 machinery parts, and 100 composites to build a facility that reduces all environmental damage by 10% at the cost of 5 energy a tick. You must have at least 900 relationship with the New Lunar Republic to build this. You can only build 5 of these. Having 2 negates 19% environmental damage, not 20%.', 45, 1, 20000000, 20000000, 0, 0, 0, 0, 4, 0),
(42, 0, 'Build Toy Factory', 'With 100 copper, 5 machinery, and 5 precision parts, you build a Toy Factory that turns 1 plastic and 1 energy into 3 toys a tick.', 46, 1, 500000, 250000, 0, 0, 0, 0, 3, 0),
(43, 0, 'Build Toy and Candy Shop', 'For 50 copper, you build a toy and candy shop that gives your ponies 30 satisfaction a tick at the price of 4 sugar, 4 cupcakes, and 4 toys. Raises satisfaction by 10 just being built. Really, what kind of adults buy toys?', 48, 1, 400000, 400000, 0, 0, 10, 0, 4, 0),
(44, 0, 'Build Mall', 'The Mall is the ultimate in satisfaction and revenue generation. It costs 200 copper, 10 vehicle parts, 10 machinery parts, and 10 tungsten to build, and it gives 250,000 (!) GDP and 50 satisfaction a tick. However, it requires 5 vehicle parts, 2 machinery parts, 5 toys, 5 coffee, 5 vodka, 2 gems, 5 sugar, 5 cupcakes, 5 gasoline, and 10 energy a tick to operate. Remember, if you are missing enough of even one of these things, all your malls will fail to function. (Malls are serious business.) Raises satisfaction by 100 just being built- although if you''re at the level where you can build this, you probably don''t care.', 49, 1, 3000000, 3000000, 0, 0, 100, 0, 4, 0),
(45, 1, 'Build DNA Extraction Facility', 'Extract DNA from the local ponies and wildlife of the North Saddle Arabian region.\nRequires 1 sugar per tick and no resources to build, but building more than one\ncauses geometric sat loss from environmental damage.', 50, 1, 500000, 500000, 0, 0, 0, 0, 2, 1),
(46, 1, 'Build DNA Extraction Facility', 'Extract DNA from the local ponies and wildlife of the Central Saddle Arabian region.\nRequires 1 sugar per tick and no resources to build, but building more than one\ncauses geometric sat loss from environmental damage.', 51, 1, 500000, 500000, 0, 0, 0, 0, 2, 2),
(47, 1, 'Build DNA Extraction Facility', 'Extract DNA from the local ponies and wildlife of the South Saddle Arabian region.\nRequires 1 sugar per tick and no resources to build, but building more than one\ncauses geometric sat loss from environmental damage.', 52, 1, 500000, 500000, 0, 0, 0, 0, 2, 3),
(48, 2, 'Build DNA Extraction Facility', 'Extract DNA from the local ponies and wildlife of the North Zebrican region.\nRequires 1 sugar per tick and no resources to build, but building more than one\ncauses geometric sat loss from environmental damage.', 53, 1, 500000, 500000, 0, 0, 0, 0, 2, 1),
(49, 2, 'Build DNA Extraction Facility', 'Extract DNA from the local ponies and wildlife of the Central Zebrican region.\nRequires 1 sugar per tick and no resources to build, but building more than one\ncauses geometric sat loss from environmental damage.', 54, 1, 500000, 500000, 0, 0, 0, 0, 2, 2),
(50, 2, 'Build DNA Extraction Facility', 'Extract DNA from the local ponies and wildlife of the South Zebrican region.\nRequires 1 sugar per tick and no resources to build, but building more than one\ncauses geometric sat loss from environmental damage.', 55, 1, 500000, 500000, 0, 0, 0, 0, 2, 3),
(51, 3, 'Build DNA Extraction Facility', 'Extract DNA from the local ponies and wildlife of the North Burrozilian region.\nRequires 1 sugar per tick and no resources to build, but building more than one\ncauses geometric sat loss from environmental damage.', 56, 1, 500000, 500000, 0, 0, 0, 0, 2, 1),
(52, 3, 'Build DNA Extraction Facility', 'Extract DNA from the local ponies and wildlife of the Central Burrozilian region.\nRequires 1 sugar per tick and no resources to build, but building more than one\ncauses geometric sat loss from environmental damage.', 57, 1, 500000, 500000, 0, 0, 0, 0, 2, 2),
(53, 3, 'Build DNA Extraction Facility', 'Extract DNA from the local ponies and wildlife of the South Burrozilian region.\nRequires 1 sugar per tick and no resources to build, but building more than one\ncauses geometric sat loss from environmental damage.', 58, 1, 500000, 500000, 0, 0, 0, 0, 2, 3),
(54, 4, 'Build DNA Extraction Facility', 'Extract DNA from the local ponies and wildlife of the North Przewalskian region.\nRequires 1 sugar per tick and no resources to build, but building more than one\ncauses geometric sat loss from environmental damage.', 59, 1, 500000, 500000, 0, 0, 0, 0, 2, 1),
(55, 4, 'Build DNA Extraction Facility', 'Extract DNA from the local ponies and wildlife of the Central Przewalskian region.\nRequires 1 sugar per tick and no resources to build, but building more than one\ncauses geometric sat loss from environmental damage.', 60, 1, 500000, 500000, 0, 0, 0, 0, 2, 2),
(56, 4, 'Build DNA Extraction Facility', 'Extract DNA from the local ponies and wildlife of the South Przewalskian region.\nRequires 1 sugar per tick and no resources to build, but building more than one\ncauses geometric sat loss from environmental damage.', 61, 1, 500000, 500000, 0, 0, 0, 0, 2, 3),
(57, 0, 'Build Forbidden Research Facility', 'Requires 1000 copper, 300 machinery parts, and 300 precision parts to build. Every turn, this facility requires 20 DNA from each of the dozen region and subregion combinations: a total of 240 DNA per tick. Reduces relationship with both the Solar Empire and New Lunar Republic by 15 a tick. Provides 1 Forbidden Research a tick. With 200 Forbidden Research... (Wait 1 tick after the research is complete.)', 74, 1, 1000000000, 1000000000, 0, 0, 0, 0, 9, 0),
(58, 4, 'Build Solar Collector', 'For a token payment of 100,000 bits, the Solar Empire will build a Solar Collector on your Przewalskian steppes. Generates 1 energy a tick at no cost. You can only have two of these. You still lose sat for not having any buildings if you only have these.', 78, 1, 100000, 100000, 0, 0, 0, 0, 2, 0),
(59, 4, 'Build Tidal Generator', 'For a token payment of 100,000 bits, the New Lunar Republic will build a Tidal Generator on your Przewalskian coast. Generates 1 energy a tick at no cost. You can only have two of these. You still lose sat for not having any buildings if you only have these.', 79, 1, 100000, 100000, 0, 0, 0, 0, 2, 0),
(60, 4, 'Receive Factory Aid', 'As part of an experimental joint development initiative, the Solar Empire and New Lunar Republic are building basic factories in new Przewalskian nations for only 100,000 bits. You can only receive one of these.', 5, 1, 100000, 100000, 0, 0, 0, 0, 5, 0),
(61, 4, 'Upgrade Plastics Factory', 'For 20 machinery parts, 5 precision parts, and 5 composites, you upgrade one of your plastics factories to a more efficient model available only in Przewalskia. This model produces 5 plastics per tick with 4 oil and 1 energy. Environmental penalties slowly accrue after each one converted beyond the first, but if you''re advanced enough to build this, you''ve already figured out why it''s a good idea...', 80, 1, 1000000, 1000000, 0, 0, 10, 0, 3, 0),
(62, 0, 'Ship Tungsten to the Solar Empire', 'Send 8 tungsten to the Solar Empire to better your relationship with it by 1.', 0, 0, 0, 0, 1, 0, 0, 0, 8, 0),
(63, 0, 'Ship Tungsten to the New Lunar Republic', 'Send 8 tungsten to the New Lunar Republic to better your relationship with it by 1.', 0, 0, 0, 0, 0, 1, 0, 0, 8, 0);

-- --------------------------------------------------------

--
-- Table structure for table `resourcedefs`
--

CREATE TABLE IF NOT EXISTS `resourcedefs` (
  `resource_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `is_building` tinyint(1) NOT NULL,
  `is_tradeable` tinyint(1) NOT NULL,
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `se_relation` int(11) NOT NULL,
  `nlr_relation` int(11) NOT NULL,
  `satisfaction` int(11) NOT NULL,
  `gdp` int(11) NOT NULL,
  `satisfaction_on_destroy` int(11) NOT NULL,
  `bad_min` int(11) NOT NULL,
  `bad_div` int(11) NOT NULL,
  PRIMARY KEY (`resource_id`),
  KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=81 ;

--
-- Dumping data for table `resourcedefs`
--

INSERT INTO `resourcedefs` (`resource_id`, `is_building`, `is_tradeable`, `name`, `description`, `se_relation`, `nlr_relation`, `satisfaction`, `gdp`, `satisfaction_on_destroy`, `bad_min`, `bad_div`) VALUES
(1, 0, 1, 'Oil', 'The stuff of life and the backbone of the &gt;CLOP economy.', 0, 0, 0, 0, 0, 0, 0),
(2, 0, 1, 'Copper', 'It''s a good thing that copper is so common, because it''s fundamental to the manufacture of every technological component.', 0, 0, 0, 0, 0, 0, 0),
(3, 0, 1, 'Sugar', 'A pony without sugar is an unhappy pony. Unhappy ponies will overthrow you and trot on your corpse. Keep your ponies happy. Give them sugar.', 0, 0, 0, 0, 0, 0, 0),
(4, 0, 0, 'Energy', 'Batteries, watts, juice- call it what you want, you need more of this, whether you burn oil for it or not.', 0, 0, 0, 0, 0, 0, 0),
(5, 1, 0, 'Basic Factory', 'Little more than a series of primitive CNC machines and some aging control systems, this factory makes everything from basic gun parts to pieces of more advanced factories. It''s not the greatest place to work (-1 sat/turn), but on the bright side, it turns one unit of energy into 10k money every 2 hours, depending on your economy type. Building more than 20 hurts the environment.', 0, 0, -1, 10000, 0, 20, 10),
(6, 1, 0, 'Basic Oil Well', 'Inefficient, dirty as hell, and a horrible place to work, this well provides 5 units of the black goo that runs the world every 2 hours.', 0, 0, -2, 0, 1, 10, 10),
(7, 1, 0, 'Basic Copper Mine', 'A nasty and downright dangerous place to work, this mine provides 5 units of copper every 2 hours.', 0, 0, -2, 0, 1, 10, 10),
(8, 1, 0, 'Basic Sugar Farm', 'Growing sugar beets or sugarcane according to the local climate, this Sugar Farm produces 5 units of raw sugar every 2 hours. It uses nothing but pure horsepower, making it a backbreaking place to work for the local horses.', 0, 0, -2, 0, 1, 10, 10),
(9, 0, 1, 'Vehicle Parts', 'These vehicle parts have many uses, first among them being to make life easier on ponies who work in farms, mines, and oil wells, turning a nasty job into a slightly less nasty one.', 0, 0, 0, 0, 0, 0, 0),
(10, 0, 1, 'Machinery Parts', 'These machinery parts are used in the manufacture of advanced structures.', 0, 0, 0, 0, 0, 0, 0),
(11, 1, 0, 'Oil Combustion Facility', 'Much more efficient and less polluting than burning oil by hand, the Oil Combustion Facility yields 12 energy out of 8 oil every 2 hours. (If you have less than 8 oil, it simply does nothing.)', 0, 0, -2, 0, 0, 5, 5),
(12, 1, 0, 'Bakery', 'Every turn, this bakery performs the vital task of turning 1 unit of energy and 2 of sugar into 3 huge boxes of cupcakes. It''s also a great place to work.', 0, 0, 1, 0, -5, 0, 0),
(13, 0, 1, 'Cupcakes', 'Distributing these baked treats brings your little ponies'' happiness up quickly.', 0, 0, 0, 0, 0, 0, 0),
(14, 1, 0, 'Mechanized Oil Well', 'This oil well uses machinery and vehicles to more efficiently extract oil (8 per turn) and make life easier on the Saddle Arabians who work there (-1 sat). It requires 1 energy every turn, but you should have plenty of that.', 0, 0, -1, 0, 0, 20, 5),
(15, 1, 0, 'Mechanized Copper Mine', 'Machinery and automated minecarts make a lot of difference to a mine, both for the mine''s overall efficiency (-1 energy for 8 copper per turn) and the zebras who work there (-1 sat).', 0, 0, -1, 0, 0, 20, 5),
(16, 1, 0, 'Mechanized Sugar Farm', 'Say what you want about the earth pony way, but industrial agriculture definitely beats having to grow the stuff yourself. -1 energy and -1 sat for 8 sugar every turn.', 0, 0, -1, 0, 0, 20, 5),
(17, 1, 0, 'Vodka Production Facility', 'Ferment 5 sugar a turn into 5 vodka! Requires 15 copper, 5 vehicle parts, and 5 mechanical parts to build. Does nothing for satisfaction one way or another, as the ponies working there are high on their own supply.', 0, 0, 0, 0, 0, 0, 0),
(18, 0, 1, 'Vodka', 'Serve it in bars or fuel your communists with it.', 0, 0, 0, 0, 0, 0, 0),
(19, 1, 0, 'Coffee Farm', 'As ponies have no hands, mechanical help is absolutely necessary to pick and transport these beans. Requires 10 vehicle parts and 5 mechanical parts to build. Uses 1 energy and costs 1 satisfaction.', 0, 0, -1, 0, 0, 15, 10),
(20, 0, 1, 'Coffee', 'Serve it in coffee shops or feed your capitalists with it.', 0, 0, 0, 0, 0, 0, 0),
(21, 1, 0, 'Coffee Shop', 'This coffee shop uses 3 coffee and 1 cupcakes a turn to provide your ponies with 10 satisfaction. No energy is required.', 0, 0, 10, 0, -10, 0, 0),
(22, 1, 0, 'Bar', 'Unlike bars for humans, this one serves cupcakes with its vodka. Uses 3 coffee and 1 cupcakes a turn to provide your ponies with 10 satisfaction. No energy is required.', 0, 0, 10, 0, -10, 0, 0),
(23, 1, 0, 'Gasoline Refinery', 'This refinery turns oil into gasoline.', 0, 0, 0, 0, 0, 0, 0),
(24, 1, 0, 'Gasoline Combustion Facility', 'This combustion facility burns oil to gasoline for no satisfaction loss.', 0, 0, 0, 0, 0, 0, 0),
(25, 0, 1, 'Gasoline', 'This refined product of oil fuels government types and buildings.', 0, 0, 0, 0, 0, 0, 0),
(26, 0, 1, 'Gems', 'These little crystals are useful in the manufacturing of precision parts.', 0, 0, 0, 0, 0, 0, 0),
(27, 0, 1, 'Tungsten', 'This tough substance is useful in the construction of alloys and composites.', 0, 0, 0, 0, 0, 0, 0),
(28, 0, 1, 'Plastics', 'Plastics, derived from oil, are useful in the creation of composites.', 0, 0, 0, 0, 0, 0, 0),
(29, 0, 1, 'Precision Parts', 'These extremely precise parts are useful for unicorns and in tungsten mining.', 0, 0, 0, 0, 0, 0, 0),
(30, 0, 1, 'Composites', 'These composites are useful in fracking and tank armor.', 0, 0, 0, 0, 0, 0, 0),
(31, 1, 0, 'Advanced Factory', 'This factory makes precision parts and composites.', 0, 0, -1, 20000, -5, 5, 5),
(32, 1, 0, 'Gem Mine', 'This mine has caffeinated ponies digging gems out of the ground.', 0, 0, -1, 0, 0, 7, 5),
(33, 1, 0, 'Tungsten Mine', 'This mine has drunken ponies digging up tungsten.', 0, 0, -1, 0, 0, 7, 5),
(34, 1, 0, 'Plastics Factory', 'This facility makes plastics out of oil.', 0, 0, -1, 0, 0, 7, 5),
(35, 1, 0, 'Oil Fracker', 'This facility digs deep into the ground to frack oil.', 0, 0, -2, 0, 0, 5, 5),
(36, 1, 0, 'Barracks', 'For 500 copper, 500 energy, 50 vehicle parts, and 50 machinery parts, you build a Barracks that increases your new forces'' training by 1. More Barracks stack. However, building more than 1 Barracks very much worries your little ponies.', 0, 0, 0, 0, 10, 1, 1),
(37, 1, 0, 'Video Arcade', 'For 50 copper and 50 energy, you build a video arcade that uses 6 energy and 1 cupcakes a turn to yield 10 satisfaction.', 0, 0, 10, 0, -5, 0, 0),
(38, 1, 0, 'Statue', 'Use 20,000 copper to build a huge statue to inspire your ponies and provide 1 satisfaction a turn. Requires no constant inputs.', 0, 0, 1, 0, -50, 0, 0),
(39, 1, 0, 'Sun Worship Center', 'For 50 copper and 20 plastics, you build a sun worship center that uses 2 coffee and 6 energy to raise your relationship with the Solar Empire by 1 every turn.', 1, 0, 0, 0, 0, 0, 0),
(40, 1, 0, 'Moon Worship Center', 'For 50 copper and 20 plastics, you build a moon worship center that uses 2 vodka and 6 energy to raise your relationship with the New Lunar Republic by 1 every turn.', 0, 1, 0, 0, 0, 0, 0),
(41, 1, 0, 'Drug Farm', 'For 200,000 bits and no resources, you plant a drug farm. It produces 10 drugs a turn that can be sold to the Solar Empire or New Lunar Republic for 20,000 bits each and a relationship hit of 1 with the nation that you sell them to. While it has no effect on satisfaction, its mere existence reduces your relationship with both the Solar Empire and New Lunar Republic by 1 a turn.', -1, -1, 0, 0, 0, 0, 0),
(42, 0, 1, 'Drugs', 'Sell these drugs to the Solar Empire or New Lunar Republic to get 20,000 bits at the cost of 1 relationship.', 0, 0, 0, 0, 0, 0, 0),
(44, 1, 0, 'Solar Environmental Facility', 'Through advanced technology and solar magic, you use 500 copper, 100 machinery parts, and 100 composites to build a facility that reduces all environmental damage by 10% at the cost of 5 energy a turn. You must have at least 900 relationship with the Solar Empire to build this. You can only build 5 of these. Having 2 negates 19% environmental damage, not 20%.', 0, 0, 0, 0, 0, 0, 0),
(45, 1, 0, 'Lunar Environmental Facility', 'Through advanced technology and the aid of NLR unicorns, you use 500 copper, 100 machinery parts, and 100 composites to build a facility that reduces all environmental damage by 10% at the cost of 5 energy a turn. You must have at least 900 relationship with the New Lunar Republic to build this. You can only build 5 of these. Having 2 negates 19% environmental damage, not 20%.', 0, 0, 0, 0, 0, 0, 0),
(46, 1, 0, 'Toy Factory', 'With 100 copper, 5 machinery, and 5 precision parts, you build a Toy Factory that turns 1 plastic and 1 energy into 3 toys a turn.', 0, 0, 0, 0, 0, 0, 0),
(47, 0, 1, 'Toys', 'These little bits of plastic are used for selling to those who collect such things. But what kind of adult does that?', 0, 0, 0, 0, 0, 0, 0),
(48, 1, 0, 'Toy and Candy Shop', 'For 50 copper, you build a toy and candy shop that gives your ponies 30 satisfaction a turn at the price of 4 sugar, 4 cupcakes, and 4 toys. Raises satisfaction by 10 just being built. Really, what kind of adults buy toys?', 0, 0, 30, 0, -20, 0, 0),
(49, 1, 0, 'Mall', 'The Mall is the ultimate in satisfaction and revenue generation. It gives 400,000 (!) GDP and 50 satisfaction a turn. However, it requires 5 vehicle parts, 2 machinery parts, 5 toys, 5 coffee, 5 vodka, 2 gems, 5 sugar, 5 cupcakes, 5 gasoline, and 10 energy a turn to operate. Remember, if you are missing enough of even one of these things, all your malls will fail to function. (Malls are serious business.)', 0, 0, 50, 250000, 0, 0, 0),
(50, 1, 0, 'DNA Extraction Facility - N. SA', 'Extract DNA from the local ponies and wildlife of the North Saddle Arabian region.\nRequires no resources per turn.\nBuilding more than one causes quadratic sat loss from environmental\ndamage.', 0, 0, 0, 0, 0, 1, 1),
(51, 1, 0, 'DNA Extraction Facility - C. SA', 'Extract DNA from the local ponies and wildlife of the Central Saddle Arabian region.\nRequires no resources per turn.\nBuilding more than one causes quadratic sat loss from environmental\ndamage.', 0, 0, 0, 0, 0, 1, 1),
(52, 1, 0, 'DNA Extraction Facility - S. SA', 'Extract DNA from the local ponies and wildlife of the South Saddle Arabian region.\nRequires no resources per turn.\nBuilding more than one causes quadratic sat loss from environmental\ndamage.', 0, 0, 0, 0, 0, 1, 1),
(53, 1, 0, 'DNA Extraction Facility - N. Zebrica', 'Extract DNA from the local ponies and wildlife of the North Zebrican region.\nRequires no resources per turn.\nBuilding more than one causes quadratic sat loss from environmental\ndamage.', 0, 0, 0, 0, 0, 1, 1),
(54, 1, 0, 'DNA Extraction Facility - C. Zebrica', 'Extract DNA from the local ponies and wildlife of the Central Zebrican region.\nRequires no resources per turn.\nBuilding more than one causes quadratic sat loss from environmental\ndamage.', 0, 0, 0, 0, 0, 1, 1),
(55, 1, 0, 'DNA Extraction Facility - S. Zebrica', 'Extract DNA from the local ponies and wildlife of the South Zebrican region.\nRequires no resources per turn.\nBuilding more than one causes quadratic sat loss from environmental\ndamage.', 0, 0, 0, 0, 0, 1, 1),
(56, 1, 0, 'DNA Extraction Facility - N. Burrozil', 'Extract DNA from the local ponies and wildlife of the North Burrozilian region.\nRequires no resources per turn.\nBuilding more than one causes quadratic sat loss from environmental\ndamage.', 0, 0, 0, 0, 0, 1, 1),
(57, 1, 0, 'DNA Extraction Facility - C. Burrozil', 'Extract DNA from the local ponies and wildlife of the Central Burrozilian region.\nRequires no resources per turn.\nBuilding more than one causes quadratic sat loss from environmental\ndamage.', 0, 0, 0, 0, 0, 1, 1),
(58, 1, 0, 'DNA Extraction Facility - S. Burrozil', 'Extract DNA from the local ponies and wildlife of the South Burrozilian region.\nRequires no resources per turn.\nBuilding more than one causes quadratic sat loss from environmental\ndamage.', 0, 0, 0, 0, 0, 1, 1),
(59, 1, 0, 'DNA Extraction Facility - N. Prze', 'Extract DNA from the local ponies and wildlife of the North Przewalskian region.\nRequires no resources per turn.\nBuilding more than one causes quadratic sat loss from environmental\ndamage.', 0, 0, 0, 0, 0, 1, 1),
(60, 1, 0, 'DNA Extraction Facility - C. Prze', 'Extract DNA from the local ponies and wildlife of the Central Przewalskian region.\nRequires no resources per turn.\nBuilding more than one causes quadratic sat loss from environmental\ndamage.', 0, 0, 0, 0, 0, 1, 1),
(61, 1, 0, 'DNA Extraction Facility - S. Prze', 'Extract DNA from the local ponies and wildlife of the South Przewalskian region.\nRequires no resources per turn.\nBuilding more than one causes quadratic sat loss from environmental\ndamage.', 0, 0, 0, 0, 0, 1, 1),
(62, 0, 1, 'DNA - North Saddle Arabia', 'DNA from the North Saddle Arabian region.', 0, 0, 0, 0, 0, 0, 0),
(63, 0, 1, 'DNA - Central Saddle Arabia', 'DNA from the Central Saddle Arabian region.', 0, 0, 0, 0, 0, 0, 0),
(64, 0, 1, 'DNA - South Saddle Arabia', 'DNA from the South Saddle Arabian region.', 0, 0, 0, 0, 0, 0, 0),
(65, 0, 1, 'DNA - North Zebrica', 'DNA from the North Zebrican region.', 0, 0, 0, 0, 0, 0, 0),
(66, 0, 1, 'DNA - Central Zebrica', 'DNA from the Central Zebrican region.', 0, 0, 0, 0, 0, 0, 0),
(67, 0, 1, 'DNA - South Zebrica', 'DNA from the South Zebrican region.', 0, 0, 0, 0, 0, 0, 0),
(68, 0, 1, 'DNA - North Burrozil', 'DNA from the North Burrozilian region.', 0, 0, 0, 0, 0, 0, 0),
(69, 0, 1, 'DNA - Central Burrozil', 'DNA from the Central Burrozilian region.', 0, 0, 0, 0, 0, 0, 0),
(70, 0, 1, 'DNA - South Burrozil', 'DNA from the South Burrozilian region.', 0, 0, 0, 0, 0, 0, 0),
(71, 0, 1, 'DNA - North Przewalskia', 'DNA from the North Przewalskian region.', 0, 0, 0, 0, 0, 0, 0),
(72, 0, 1, 'DNA - Central Przewalskia', 'DNA from the Central Przewalskian region.', 0, 0, 0, 0, 0, 0, 0),
(73, 0, 1, 'DNA - South Przewalskia', 'DNA from the South Przewalskian region.', 0, 0, 0, 0, 0, 0, 0),
(74, 1, 0, 'Forbidden Research Facility', 'Conduct forbidden research here.', -15, -15, 0, 0, 0, 0, 0),
(75, 0, 0, 'Forbidden Research', 'This research has been forbidden.', 0, 0, 0, 0, 0, 0, 0),
(76, 1, 0, 'Alicornification Facility', 'You know what this does.', 0, 0, 0, 0, 0, 0, 0),
(77, 0, 0, 'Apotheosis Serum', 'You know what this is for.', 0, 0, 0, 0, 0, 0, 0),
(78, 1, 0, 'Solar Collector', 'This solar collector provides 1 energy a turn to Przewalskians.', 0, 0, 0, 0, 0, 0, 0),
(79, 1, 0, 'Tidal Generator', 'This tidal generator provides 1 energy a turn to Przewalskians.', 0, 0, 0, 0, 0, 0, 0),
(80, 1, 0, 'Przewalskian Plastics Factory', 'This factory produces plastics more efficiently.', 0, 0, -1, 0, 0, 1, 5);

-- --------------------------------------------------------

--
-- Table structure for table `resourceeffects`
--

CREATE TABLE IF NOT EXISTS `resourceeffects` (
  `resource_id` int(10) unsigned NOT NULL,
  `affectedresource_id` int(10) unsigned NOT NULL,
  `amount` int(11) NOT NULL,
  KEY `resource_id` (`resource_id`),
  KEY `affectedresource_id` (`affectedresource_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `resourceeffects`
--

INSERT INTO `resourceeffects` (`resource_id`, `affectedresource_id`, `amount`) VALUES
(6, 1, 5),
(7, 2, 5),
(8, 3, 5),
(11, 4, 12),
(12, 13, 3),
(14, 1, 8),
(15, 2, 8),
(16, 3, 8),
(17, 18, 5),
(19, 20, 5),
(23, 25, 8),
(24, 4, 12),
(32, 26, 3),
(33, 27, 4),
(34, 28, 4),
(35, 1, 20),
(41, 42, 10),
(46, 47, 3),
(50, 62, 1),
(51, 63, 1),
(52, 64, 1),
(53, 65, 1),
(54, 66, 1),
(55, 67, 1),
(56, 68, 1),
(57, 69, 1),
(58, 70, 1),
(59, 71, 1),
(60, 72, 1),
(61, 73, 1),
(74, 75, 1),
(76, 77, 1),
(78, 4, 1),
(79, 4, 1),
(80, 28, 5);

-- --------------------------------------------------------

--
-- Table structure for table `resourcerequirements`
--

CREATE TABLE IF NOT EXISTS `resourcerequirements` (
  `resource_id` int(10) unsigned NOT NULL,
  `requiredresource_id` int(10) unsigned NOT NULL,
  `amount` int(11) NOT NULL,
  KEY `resource_id` (`resource_id`),
  KEY `requiredresource_id` (`requiredresource_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `resourcerequirements`
--

INSERT INTO `resourcerequirements` (`resource_id`, `requiredresource_id`, `amount`) VALUES
(11, 1, 8),
(5, 4, 1),
(12, 3, 2),
(12, 4, 1),
(14, 4, 1),
(15, 4, 1),
(16, 4, 1),
(17, 3, 5),
(17, 4, 1),
(19, 4, 1),
(21, 20, 3),
(22, 18, 3),
(21, 13, 1),
(22, 13, 1),
(23, 1, 10),
(24, 25, 8),
(32, 20, 1),
(32, 4, 1),
(33, 18, 1),
(33, 4, 1),
(31, 4, 2),
(34, 4, 1),
(34, 1, 4),
(35, 4, 2),
(37, 4, 5),
(37, 13, 1),
(39, 20, 2),
(39, 4, 6),
(40, 18, 2),
(40, 4, 6),
(44, 4, 5),
(45, 4, 5),
(46, 4, 1),
(46, 28, 1),
(48, 3, 4),
(48, 13, 4),
(48, 47, 4),
(49, 9, 5),
(49, 10, 2),
(49, 47, 5),
(49, 20, 5),
(49, 18, 5),
(49, 26, 2),
(49, 3, 5),
(49, 13, 5),
(49, 25, 5),
(49, 4, 10),
(74, 62, 20),
(74, 63, 20),
(74, 64, 20),
(74, 65, 20),
(74, 66, 20),
(74, 67, 20),
(74, 68, 20),
(74, 69, 20),
(74, 70, 20),
(74, 71, 20),
(74, 72, 20),
(74, 73, 20),
(50, 3, 1),
(51, 3, 1),
(52, 3, 1),
(53, 3, 1),
(54, 3, 1),
(55, 3, 1),
(56, 3, 1),
(57, 3, 1),
(58, 3, 1),
(59, 3, 1),
(60, 3, 1),
(61, 3, 1),
(76, 3, 50),
(76, 4, 100),
(80, 1, 4),
(80, 4, 1);

-- --------------------------------------------------------

--
-- Table structure for table `weapondefs`
--

CREATE TABLE IF NOT EXISTS `weapondefs` (
  `weapon_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `type` int(10) unsigned NOT NULL,
  `dmg_cavalry` decimal(5,2) NOT NULL,
  `dmg_tanks` decimal(5,2) NOT NULL,
  `dmg_pegasi` decimal(5,2) NOT NULL,
  `dmg_unicorns` decimal(5,2) NOT NULL,
  `dmg_naval` decimal(5,2) NOT NULL,
  PRIMARY KEY (`weapon_id`),
  KEY `type` (`type`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=20 ;

--
-- Dumping data for table `weapondefs`
--

INSERT INTO `weapondefs` (`weapon_id`, `name`, `type`, `dmg_cavalry`, `dmg_tanks`, `dmg_pegasi`, `dmg_unicorns`, `dmg_naval`) VALUES
(1, 'PRC-E6', 1, '0.80', '0.80', '0.80', '0.80', '0.80'),
(2, 'PRC-E7', 1, '0.90', '0.90', '0.90', '0.90', '0.90'),
(3, 'PRC-E8', 1, '1.00', '1.00', '1.00', '1.00', '1.10'),
(4, 'ACFU', 1, '1.50', '1.00', '1.00', '1.00', '1.00'),
(5, 'ATFU', 1, '1.00', '1.50', '1.00', '1.00', '1.00'),
(6, 'APFU', 1, '1.00', '1.00', '1.50', '1.00', '1.00'),
(7, 'AUFU', 1, '1.00', '1.00', '1.00', '1.50', '1.00'),
(8, 'K9P', 2, '1.30', '1.10', '1.10', '1.10', '1.10'),
(9, 'ELBO-GRS', 2, '1.20', '1.40', '1.20', '1.20', '1.20'),
(10, 'Chem-Light Battery', 2, '1.25', '1.25', '1.25', '1.70', '1.25'),
(11, 'Prop Wash', 3, '1.50', '1.25', '1.25', '1.25', '1.25'),
(12, 'Steam Bucket', 3, '1.30', '1.30', '1.30', '1.30', '1.60'),
(13, 'Canopy Lights', 3, '1.40', '2.20', '1.40', '1.40', '1.40'),
(14, 'Long Stand', 4, '2.20', '1.90', '1.90', '1.90', '1.90'),
(15, 'Long Weight', 4, '1.95', '1.95', '1.95', '1.95', '2.20'),
(16, 'Grid Squares', 4, '2.00', '2.00', '3.00', '2.00', '2.00'),
(17, 'Shoreline', 5, '1.00', '1.35', '1.00', '1.00', '1.00'),
(18, 'Water Hammer', 5, '1.10', '1.10', '1.10', '1.35', '1.10'),
(19, 'Waterline Eraser', 5, '1.25', '1.25', '1.25', '1.25', '1.25');

-- --------------------------------------------------------

--
-- Table structure for table `weaponrecipeitems`
--

CREATE TABLE IF NOT EXISTS `weaponrecipeitems` (
  `weaponrecipe_id` int(11) NOT NULL,
  `resource_id` int(11) unsigned NOT NULL,
  `is_used_up` tinyint(1) NOT NULL,
  `amount` int(10) unsigned NOT NULL,
  UNIQUE KEY `weaponrecipe_id_2` (`weaponrecipe_id`,`resource_id`),
  KEY `resource_id` (`resource_id`),
  KEY `weaponrecipe_id` (`weaponrecipe_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `weaponrecipeitems`
--

INSERT INTO `weaponrecipeitems` (`weaponrecipe_id`, `resource_id`, `is_used_up`, `amount`) VALUES
(1, 2, 1, 5),
(2, 2, 1, 5),
(2, 27, 1, 1),
(3, 2, 1, 8),
(3, 5, 0, 1),
(3, 10, 1, 1),
(4, 2, 1, 10),
(4, 10, 1, 1),
(4, 31, 0, 1),
(5, 2, 1, 8),
(5, 10, 1, 1),
(5, 27, 1, 1),
(5, 31, 0, 1),
(6, 2, 1, 8),
(6, 10, 1, 1),
(6, 28, 1, 1),
(6, 31, 0, 1),
(7, 2, 1, 8),
(7, 10, 1, 1),
(7, 29, 1, 1),
(7, 31, 0, 1),
(8, 2, 1, 15),
(9, 2, 1, 15),
(9, 5, 0, 1),
(9, 10, 1, 2),
(10, 2, 1, 15),
(10, 29, 1, 1),
(10, 31, 0, 1),
(11, 2, 1, 10),
(11, 29, 1, 1),
(12, 2, 1, 10),
(12, 5, 0, 1),
(12, 29, 1, 2),
(13, 2, 1, 10),
(13, 29, 1, 3),
(13, 31, 0, 1),
(14, 29, 1, 5),
(15, 5, 0, 1),
(15, 29, 1, 10),
(16, 10, 1, 3),
(16, 29, 1, 10),
(16, 31, 0, 1),
(17, 2, 1, 5),
(17, 5, 0, 1),
(17, 10, 1, 1),
(18, 2, 1, 10),
(18, 10, 1, 2),
(18, 31, 0, 1),
(19, 2, 1, 15),
(19, 10, 1, 3),
(19, 31, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `weaponrecipes`
--

CREATE TABLE IF NOT EXISTS `weaponrecipes` (
  `weaponrecipe_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `weapon_id` int(10) unsigned NOT NULL,
  `amount` int(10) unsigned NOT NULL DEFAULT '1',
  `cost` int(10) unsigned NOT NULL,
  PRIMARY KEY (`weaponrecipe_id`),
  KEY `weapon_id` (`weapon_id`),
  KEY `cost` (`cost`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=20 ;

--
-- Dumping data for table `weaponrecipes`
--

INSERT INTO `weaponrecipes` (`weaponrecipe_id`, `name`, `description`, `weapon_id`, `amount`, `cost`) VALUES
(1, 'Build PRC-E6', '', 1, 1, 20000),
(2, 'Build PRC-E7', '', 2, 1, 30000),
(3, 'Build PRC-E8', '', 3, 1, 40000),
(4, 'Build ACFU', '', 4, 1, 50000),
(5, 'Build ATFU', '', 5, 1, 50000),
(6, 'Build APFU', '', 6, 1, 50000),
(7, 'Build AUFU', '', 7, 1, 50000),
(8, 'Build K9P', '', 8, 1, 50000),
(9, 'Build ELBO-GRS', '', 9, 1, 60000),
(10, 'Build Chem-Light Battery', '', 10, 1, 70000),
(11, 'Build Prop Wash', '', 11, 1, 50000),
(12, 'Build Steam Bucket', '', 12, 1, 60000),
(13, 'Build Canopy Lights', '', 13, 1, 70000),
(14, 'Build Long Stand', '', 14, 1, 80000),
(15, 'Build Long Weight', '', 15, 1, 100000),
(16, 'Build Grid Squares', '', 16, 1, 120000),
(17, 'Build Shoreline', '', 17, 1, 60000),
(18, 'Build Water Hammer', '', 18, 1, 80000),
(19, 'Build Waterline Eraser', '', 19, 1, 100000);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
