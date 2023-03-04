USE clopus_elements;

-- phpMyAdmin SQL Dump
-- version 4.0.10.14
-- http://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Generation Time: May 06, 2016 at 02:15 PM
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
-- Table structure for table `abilities`
--

CREATE TABLE IF NOT EXISTS `abilities` (
  `ability_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) CHARACTER SET latin1 NOT NULL,
  `friendlyname` varchar(64) CHARACTER SET latin1 NOT NULL,
  `description` text CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`ability_id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=15 ;

--
-- Dumping data for table `abilities`
--

INSERT INTO `abilities` (`ability_id`, `name`, `friendlyname`, `description`) VALUES
(1, 'seerippedoff', 'See Ripped Off', 'You know when you have been ripped off when purchasing something or making a deal.'),
(2, 'logmarketplace', 'Log Marketplace', 'Log when you buy or sell items in the Marketplace.'),
(3, 'encouraged', 'Encouraged', '5 extra production a tick from being encouraged.'),
(4, 'alliancespendresources', 'Spend/Bank Alliance Resources', 'You can spend your alliance''s resources on increasing its satisfaction and increasing the satisfaction of other alliances.'),
(5, 'allianceinviteusers', 'Invite Alliance Members', 'You may invite users to your alliance.'),
(6, 'alliancegrantabilities', 'Grant Alliance Abilities', 'You may grant alliance abilities to other users in your alliance.'),
(7, 'alliancekickusers', 'Kick Alliance Members', 'You may kick users out of your alliance.'),
(8, 'alliancegiveresources', 'Give Resources to Alliance', 'You may give resources to your alliance.'),
(9, 'alliancetakeresources', 'Take Resources from Alliance', 'You may take resources from your alliance.'),
(10, 'alliancemessaging', 'Alliance Messaging', 'Control all messaging, both internal and external, for the alliance.'),
(11, 'seeattackattempts', 'See Attack Attempts', 'See when someone has attempted but failed to attack you.'),
(12, 'seespyattempts', 'See Spy Attempts', 'See when someone has attempted but failed to spy on you.'),
(13, 'alliancemakewar', 'Make War (Alliance)', 'Make war on behalf of your alliance.'),
(14, 'alliancemakedeals', 'Make Deals (Alliance)', 'Make deals on behalf of your alliance.');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

--
-- Table structure for table `constants`
--

CREATE TABLE IF NOT EXISTS `constants` (
  `name` varchar(64) CHARACTER SET latin1 NOT NULL,
  `friendlyname` varchar(64) CHARACTER SET latin1 NOT NULL,
  `type` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `value` int(11) NOT NULL,
  `description` text CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`name`),
  KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `constants`
--

INSERT INTO `constants` (`name`, `friendlyname`, `type`, `value`, `description`) VALUES
('allianceburdenrequired', 'Alliance Burden Required', 'alliancewar', 40, 'How much Burden is required to perform this attack against an alliance.'),
('alliancecompassionforburden', 'Compassion for Burden (Alliance)', 'alliancewar', 40, 'How much Compassion it costs to recall a Burden attack.'),
('alliancecompassionforcorrupt', 'Compassion for Corruption (Alliance)', 'alliancewar', 400, 'How much Compassion it costs to recall a Corruption attack.'),
('alliancecompassionforsadness', 'Compassion for Sadness (Alliance)', 'alliancewar', 80, 'How much Compassion it costs to recall a Sadness attack.'),
('alliancecompassionfortheft', 'Compassion for Theft (Alliance)', 'alliancewar', 200, 'How much Compassion is required to recall a Theft attack.'),
('alliancecorruptionrequired', 'Alliance Corruption Required', 'alliancewar', 400, 'How much Corruption is required to perform this attack against an alliance.'),
('allianceequalitytospy', 'Equality to Spy (Alliance)', 'spying', 80, 'How much Equality it takes to spy on an alliance.'),
('alliancemaliceforburden', 'Malice to Redirect Burden (Alliance)', 'alliancewar', 40, 'The amount of Malice required to redirect a Burden attack.'),
('alliancemaliceforcorrupt', 'Malice to Redirect Corruption (Alliance)', 'alliancewar', 400, 'The amount of Malice required to redirect a Corruption attack.'),
('alliancemaliceforsadness', 'Malice to Redirect Sadness (Alliance)', 'alliancewar', 100, 'The amount of Malice required to redirect a Sadness attack.'),
('alliancemalicefortheft', 'Malice to Redirect Theft (Alliance)', 'alliancewar', 200, 'How much Malice is required to redirect a Theft attack.'),
('alliancesadnessrequired', 'Alliance Sadness Required', 'alliancewar', 100, 'How much Sadness is required to perform this attack against an alliance.'),
('alliancesecurityforburden', 'Security to Prevent Burden (Alliance)', 'alliancewar', 30, 'How much Security it takes to prevent a Burden attack against an alliance.'),
('alliancesecurityfortheft', 'Security to Prevent Theft (Alliance)', 'alliancewar', 150, 'How much Security it takes to prevent a Theft attack against an alliance.'),
('allianceserenityforcorrupt', 'Serenity to Prevent Corruption (Alliance)', 'alliancewar', 300, 'How much Serenity it takes to prevent a Corruption attack against an alliance.'),
('allianceserenityforsadness', 'Serenity to Prevent Sadness (Alliance)', 'alliancewar', 75, 'How much Serenity it takes to prevent a Sadness attack against an alliance.'),
('alliancetheftrequired', 'Alliance Theft Required', 'alliancewar', 200, 'How much Theft is required to perform this attack against an alliance.'),
('allianceunitytoblock', 'Unity to Block (Alliance)', 'spying', 80, 'How much Unity it takes to block a spying attempt against an alliance.'),
('backstabbingrequired', 'Backstabbing to Break a Treaty', 'alliancewar', 600, 'How much Backstabbing it takes for an alliance to attack another alliance and completely destroy a peace treaty in the process.'),
('beneficenceforabilities', 'Beneficence for Abilities', 'useractions', 5, 'How much Beneficence is required to grant yourself an ability.'),
('benevolenceforabilities', 'Benevolence for Abilities', 'allianceactions', 5, 'How much Benevolence it costs to give your alliance an ability for one turn.'),
('brutalityrequired', 'Brutality to Attack', 'war', 400, 'The amount of Brutality required to reduce another player''s production by 1.'),
('bullshitdivisor', 'Philippy Items Per 1 Bullshit', 'philippy', 20, 'How many items can be masked with 1 Bullshit.'),
('burdenrequired', 'Burden to Attack', 'war', 60, 'The amount of Burden required to force-give another player resources.'),
('camaraderierequired', 'Camaraderie Necessary for 10 Satisfaction', 'useractions', 5, 'How much Camaraderie is required to raise an alliance member''s satisfaction by 10.'),
('charityfordeals', 'Charity for Deals', 'deals', 7, 'The amount of Charity required to deal with nothing in return.'),
('cheerrequired', 'Cheer Necessary for 10 Satisfaction', 'useractions', 5, 'How much Cheer is necessary to raise a nonmember''s satisfaction by 10.'),
('compassionforbrutal', 'Compassion for Brutality', 'war', 400, 'How much Compassion it costs to recall a Brutality attack.'),
('compassionforburden', 'Compassion for Burden', 'war', 120, 'How much Compassion it costs to recall a Burden attack.'),
('compassionforcorrupt', 'Compassion for Corruption', 'war', 300, 'How much Compassion it costs to recall a Corruption attack.'),
('compassionfordespair', 'Compassion for Despair', 'war', 80, 'How much Compassion it costs to recall a Despair attack.'),
('compassionforrobbery', 'Compassion for Robbery', 'war', 100, 'How much Compassion is required to recall a Robbery attack.'),
('corruptionrequired', 'Corruption to Attack', 'war', 300, 'The amount of Corruption required to change another player''s focus.'),
('delightdivisor', 'Philippy Items Per 1 Priority Per 1 Delight', 'philippy', 30, 'How many Philippy items can be raised in priority by 1 with 1 Delight.'),
('delightnecessary', 'Delight for Priority', 'marketplace', 3, 'How much Delight is necessary to raise a Marketplace item group''s priority by one, bumping it higher in the list.'),
('despairrequired', 'Despair to Attack', 'war', 80, 'The amount of Despair required to reduce another player''s satisfaction by 200.'),
('devotiontofocus', 'Devotion to Focus', 'useractions', 80, 'How much Devotion it takes to focus your production on a certain element. Four times the cost for double focus.'),
('embezzlementrequired', 'Embezzlement Required', 'useractions', 10, 'How much Embezzlement is required to loot the alliance bank.'),
('encouragementrequired', 'Encouragement Required', 'useractions', 4, 'How much Encouragement it takes, per tick, to raise an alliance member''s production by 5.'),
('equalitytoclopspy', 'Equality to spy on a &gt;CLOP nation', 'clop', 50, 'How much Equality it costs to spy on a nation in &gt;CLOP.'),
('equalitytospy', 'Equality to Spy', 'spying', 40, 'How much Equality it takes to spy on a user.'),
('fairnessfordeals', 'Fairness for Deals', 'deals', 10, 'The amount of Fairness required to perform a deal.'),
('faithtofocus', 'Faith to Focus', 'allianceactions', 150, 'How much Faith it takes to focus your alliance''s production. Four times the cost for double focus.'),
('favorforabilities', 'Favor for Abilities', 'allianceactions', 1, 'How much Favor it takes to grant an alliance member an ability for one tick.'),
('fealtyrequired', 'Fealty Required for Alliance Message', 'allianceactions', 5, 'Fealty required to post an alliance message.'),
('fraudnecessary', 'Fraud for Marketplace', 'marketplace', 5, 'How much Fraud is necessary, per item, to lie about what someone will receive from the Marketplace.'),
('happinessrequired', 'Happiness Necessary for 10 Satisfaction', 'useractions', 4, 'How much Happiness is required to raise your own satisfaction by 10.'),
('heroismforbrutal', 'Heroism to Attract Brutality', 'war', 100, 'How much Heroism it costs to attract a Brutality attack.'),
('heroismforburden', 'Heroism to Attract Burden', 'war', 30, 'How much Heroism it costs to attract a Burden attack.'),
('heroismforcorrupt', 'Heroism to Attract Corruption', 'war', 75, 'How much Heroism it costs to attract a Corruption attack.'),
('heroismfordespair', 'Heroism to Attract Despair', 'war', 20, 'How much Heroism it costs to attract a Despair attack.'),
('heroismforrobbery', 'Heroism to Attract Robbery', 'war', 25, 'How much Heroism is required to attract a Robbery attack.'),
('honorfordeals', 'Honor for Deals', 'deals', 20, 'How much Honor it takes to make an alliance deal.'),
('hoperequired', 'Hope Necessary for 10 Alliance Satisfaction', 'allianceactions', 10, 'How much Hope it takes to raise another alliance''s satisfaction by 10.'),
('humornecessary', 'Humor for Top Message', 'useractions', 20, 'How much Humor it takes to put a message up top.'),
('joyrequired', 'Joy Necessary for 10 Alliance Satisfaction', 'allianceactions', 8, 'How much Joy it takes to raise your own alliance''s satisfaction by 10.'),
('libeldivisor', 'Philippy Items Per 1 Libel', 'philippy', 6, 'How many offerings in Philippy can be posted under someone else''s name with 1 Wit.'),
('libelnecessary', 'Libel for Marketplace', 'marketplace', 10, 'How much Libel is necessary, per item, to lie about the originator of a Marketplace deal.'),
('liesabsorbed', 'Lies Absorbed', 'marketplace', 30, 'How much Lies it takes to automatically prevent an attempt at investigation or exposure.'),
('liesdivisor', 'Philippy Items per 1 Lies', 'philippy', 20, 'How many fake Philippy items can be blocked with 1 Lies.'),
('lootingformarketplace', 'Looting for Marketplace', 'marketplace', 10, 'How much Looting it takes per Times to steal a Marketplace item.'),
('magnanimitytouplift', 'Magnanimity to Uplift', 'allianceactions', 819, 'How much Magnaminity it takes to uplift another player into ascended status (being able to control an alliance).'),
('maliceforbrutal', 'Malice to Redirect Brutality', 'war', 400, 'The amount of Malice required to redirect a Brutality attack.'),
('maliceforburden', 'Malice to Redirect Burden', 'war', 120, 'The amount of Malice required to redirect a Burden attack.'),
('maliceforcorrupt', 'Malice to Redirect Corruption', 'war', 300, 'The amount of Malice required to redirect a Corruption attack.'),
('malicefordespair', 'Malice to Redirect Despair', 'war', 80, 'The amount of Malice required to redirect a Despair attack.'),
('maliceforrobbery', 'Malice to Redirect Robbery', 'war', 100, 'The amount of Malice required to redirect a Robbery attack.'),
('mercilessnessrequired', 'Mercilessness Required', 'war', 20, 'How much Mercilessness it costs to attack a Tier 4 player. Costs increase by lower tier.'),
('nobilityfornew', 'Nobility for New Alliance', 'allianceactions', 500, 'How much Nobility it costs to create a new alliance.'),
('nobilitytotransfer', 'Nobility to Transfer', 'allianceactions', 250, 'How much Nobility it takes to transfer control of your alliance to another player.'),
('perfidyrequired', 'Perfidy to Violate a Treaty', 'war', 300, 'How much Perfidy it takes for an alliance member to attack another alliance member in violation of a peace treaty.'),
('philippydivisor', 'Items Per 1 Philippy', 'philippy', 20, 'How many items can be posted with 1 Philippy.'),
('plentynecessary', 'Plenty for Marketplace', 'marketplace', 1, 'How much Plenty is required to place a group of items on the Marketplace.'),
('robberyrequired', 'Robbery to Attack', 'war', 100, 'How much Robbery is required to steal a resource.'),
('satisfactionpercheer', 'Satisfaction per Cheer', 'clop', 6, 'How much Satisfaction a nation gets from one point of Cheer.'),
('securityforbrutal', 'Security to Prevent Brutality', 'war', 300, 'The amount of Security needed to prevent a Brutality attack.'),
('securityforburden', 'Security to Prevent Burden', 'war', 90, 'The amount of Security needed to prevent a Burden attack.'),
('securityformarketplace', 'Security for Marketplace', 'marketplace', 10, 'How much Security it takes per Times to protect a Marketplace item.'),
('securityforrobbery', 'Security to Repel Robbery', 'war', 75, 'The amount of Security needed to prevent a Robbery attack.'),
('serenityforcorrupt', 'Serenity to Prevent Corruption', 'war', 225, 'The amount of Serenity needed to prevent a Corruption attack.'),
('serenityfordespair', 'Serenity to Prevent Despair', 'war', 60, 'The amount of Serenity needed to prevent a Despair attack.'),
('shelterforbrutal', 'Shelter to Repel Brutality', 'war', 800, 'The amount of Shelter required to repel a Brutality attack.'),
('shelterforburden', 'Shelter to Repel Burden', 'war', 240, 'The amount of Shelter required to repel a Burden attack.'),
('shelterforcorrupt', 'Shelter to Repel Corruption', 'war', 600, 'The amount of Shelter required to repel a Corruption attack.'),
('shelterfordespair', 'Shelter to Repel Despair', 'war', 160, 'The amount of Shelter required to repel a Despair attack.'),
('shelterforrobbery', 'Shelter to Repel Robbery', 'war', 200, 'How much Shelter it takes to repel a Robbery attack.'),
('treacheryabsorbed', 'Treachery to Stop a Kick', 'allianceactions', 100, 'How much Treachery it takes to repel an attempt for the alliance leader to kick you out of your alliance.'),
('treasonrequired', 'Treason Required to Attack', 'war', 100, 'How much Treason it takes to attack an alliance member.'),
('trustdivisor', 'Philippy Items Per 1 Trust', 'philippy', 25, 'How many Philippy items can be exposed with 1 Trust.'),
('trustnecessary', 'Trust Necessary', 'marketplace', 25, 'How much Trust is necessary, per item, to expose a false Marketplace item in public.'),
('truthdivisor', 'Philippy Items Per 1 Truth', 'philippy', 10, 'How many Philippy items can be inspected with 1 Truth.'),
('truthnecessary', 'Truth Necessary', 'marketplace', 10, 'How much Truth is necessary, per item, to discover if someone on the Marketplace is lying.'),
('unitytoblock', 'Unity to Block', 'spying', 40, 'How much Unity it takes to block a spying attempt.'),
('unitytoclopblock', 'Unity to block &gt;CLOP spying', 'clop', 50, 'How much banked Unity it takes to block a spying attempt against a &gt;CLOP nation.'),
('voidfordepression', 'Void for Depression', 'void', 2, 'How much Void it takes to reduce a target user''s satisfaction to 0.'),
('voidfordestruction', 'Void for Destruction', 'void', 3, 'How much Void it takes to destroy all of a target user''s resource.'),
('voidforpollution', 'Void for Pollution', 'void', 5, 'How much Void it takes to reduce a target user''s production by 1.'),
('witdivisor', 'Philippy Items Per 1 Wit', 'philippy', 25, 'How many offerings in Philippy can be made anonymous with 1 Wit.'),
('witnecessary', 'Wit for Marketplace', 'marketplace', 3, 'How much Wit is necessary, per item, to make yourself anonymous on the Marketplace.'),
('zealforburden', 'Zeal to Repel Burden (Alliance)', 'alliancewar', 80, 'The amount of Zeal required to repel a Burden attack.'),
('zealforcorrupt', 'Zeal to Repel Corruption (Alliance)', 'alliancewar', 800, 'The amount of Zeal required to repel a Corruption attack.'),
('zealforsadness', 'Zeal to Repel Sadness (Alliance)', 'alliancewar', 200, 'The amount of Zeal required to repel a Sadness attack.'),
('zealfortheft', 'Zeal to Repel Theft (Alliance)', 'alliancewar', 400, 'The amount of Zeal required to repel a Theft attack.');

-- --------------------------------------------------------

--
-- Table structure for table `elementpositions`
--

CREATE TABLE IF NOT EXISTS `elementpositions` (
  `resource_id` int(11) NOT NULL,
  `position` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `elementpositions`
--

INSERT INTO `elementpositions` (`resource_id`, `position`) VALUES
(1, 1),
(2, 2),
(4, 3),
(8, 5),
(16, 4),
(32, 6);

-- --------------------------------------------------------

--
-- Table structure for table `groupabilities`
--

CREATE TABLE IF NOT EXISTS `groupabilities` (
  `ability_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) CHARACTER SET latin1 NOT NULL,
  `friendlyname` varchar(64) CHARACTER SET latin1 NOT NULL,
  `description` text CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`ability_id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- Dumping data for table `groupabilities`
--

INSERT INTO `groupabilities` (`ability_id`, `name`, `friendlyname`, `description`) VALUES
(1, 'alertproblems', 'Alert on Problems', 'Alert on problems with your alliance.'),
(2, 'logbankactivity', 'Log Bank Activity', 'Log your alliance''s bank activity.'),
(3, 'seeallianceattacks', 'See Alliance Attacks', 'Shows the alliance when an attack is directed at an alliance member or at itself.'),
(4, 'seespyattempts', 'See Spy Attempts', 'See spying attempts against your alliance.');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- --------------------------------------------------------

--
-- Table structure for table `resourcedefs`
--

CREATE TABLE IF NOT EXISTS `resourcedefs` (
  `resource_id` int(11) NOT NULL,
  `name` varchar(64) CHARACTER SET latin1 NOT NULL,
  `elements` varchar(64) CHARACTER SET latin1 NOT NULL,
  `tier` int(10) unsigned NOT NULL,
  PRIMARY KEY (`resource_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `resourcedefs`
--

-- error: unknown column elements in field list
-- INSERT INTO `resourcedefs` (`resource_id`, `name`, `elements`, `tier`) VALUES
-- (0, 'Void', '', 0),
-- (1, 'Magic', 'Mag', 1),
-- (2, 'Loyalty', 'Loy', 1),
-- (3, 'Fealty', 'MagLoy', 2),
-- (4, 'Laughter', 'Lau', 1),
-- (5, 'Optimism', 'MagLau', 2),
-- (6, 'Camaraderie', 'LoyLau', 2),
-- (7, 'Unity', 'MagLoyLau', 3),
-- (8, 'Kindness', 'Kin', 1),
-- (9, 'Compassion', 'MagKin', 2),
-- (10, 'Devotion', 'LoyKin', 2),
-- (11, 'Faith', 'MagLoyKin', 3),
-- (12, 'Happiness', 'LauKin', 2),
-- (13, 'Joy', 'MagLauKin', 3),
-- (14, 'Love', 'LoyLauKin', 3),
-- (15, 'Fraud', '-HonGen', 4),
-- (16, 'Honesty', 'Hon', 1),
-- (17, 'Truth', 'MagHon', 2),
-- (18, 'Honor', 'LoyHon', 2),
-- (19, 'Zeal', 'MagLoyHon', 3),
-- (20, 'Humor', 'LauHon', 2),
-- (21, 'Wit', 'MagLauHon', 3),
-- (22, 'Encouragement', 'LoyLauHon', 3),
-- (23, 'Robbery', '-KinGen', 4),
-- (24, 'Fairness', 'KinHon', 2),
-- (25, 'Equality', 'MagKinHon', 3),
-- (26, 'Heroism', 'LoyKinHon', 3),
-- (27, 'Burden', '-LauGen', 4),
-- (28, 'Trust', 'LauKinHon', 3),
-- (29, 'Embezzlement', '-LoyGen', 4),
-- (30, 'Looting', '-MagGen', 4),
-- (31, 'Theft', '-Gen', 5),
-- (32, 'Generosity', 'Gen', 1),
-- (33, 'Plenty', 'MagGen', 2),
-- (34, 'Shelter', 'LoyGen', 2),
-- (35, 'Nobility', 'MagLoyGen', 3),
-- (36, 'Cheer', 'LauGen', 2),
-- (37, 'Hope', 'MagLauGen', 3),
-- (38, 'Magnanimity', 'LoyLauGen', 3),
-- (39, 'Libel', '-KinHon', 4),
-- (40, 'Charity', 'KinGen', 2),
-- (41, 'Philippy', 'MagKinGen', 3),
-- (42, 'Security', 'LoyKinGen', 3),
-- (43, 'Narcissism', '-LauHon', 4),
-- (44, 'Delight', 'LauKinGen', 3),
-- (45, 'Treason', '-LoyHon', 4),
-- (46, 'Bullshit', '-MagHon', 4),
-- (47, 'Lies', '-Hon', 5),
-- (48, 'Beneficence', 'HonGen', 2),
-- (49, 'Benevolence', 'MagHonGen', 3),
-- (50, 'Favor', 'LoyHonGen', 3),
-- (51, 'Malice', '-LauKin', 4),
-- (52, 'Growth', 'LauHonGen', 3),
-- (53, 'Corruption', '-LoyKin', 4),
-- (54, 'Brutality', '-MagKin', 4),
-- (55, 'Mercilessness', '-Kin', 5),
-- (56, 'Serenity', 'KinHonGen', 3),
-- (57, 'Perfidy', '-LoyLau', 4),
-- (58, 'Despair', '-MagLau', 4),
-- (59, 'Sadness', '-Lau', 5),
-- (60, 'Backstabbing', '-MagLoy', 4),
-- (61, 'Treachery', '-Loy', 5),
-- (62, 'Drudgery', '-Mag', 5),
-- (63, 'Harmony', 'MagLoyLauKinHonGen', 6);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
