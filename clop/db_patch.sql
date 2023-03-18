-- run after importing db dump

-- make everyone donors by default
ALTER TABLE `users`
	ALTER COLUMN `donator` SET DEFAULT TRUE,
	ADD COLUMN `funmode` BOOLEAN NOT NULL DEFAULT FALSE,
	ADD COLUMN `textdescription` TEXT COLLATE utf8_unicode_ci NOT NULL,
	ADD COLUMN `alliance_lastread` INT UNSIGNED NOT NULL DEFAULT 0;

ALTER TABLE `nations`
	ADD COLUMN `textdescription` TEXT COLLATE utf8_unicode_ci NOT NULL;

-- table for logging session hijacks attempts
CREATE TABLE `hijacks` LIKE `logins`;
ALTER TABLE `hijacks` DROP COLUMN `failed`;

-- collect history of topmessages
ALTER TABLE `topmessage`
	ADD COLUMN `message_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST;

-- feature request form
CREATE TABLE `requests` (
  `request_id` int(11) NOT NULL AUTO_INCREMENT,
  `submitter` int(10) unsigned NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text,
  `isbug` tinyint(1) NOT NULL DEFAULT '0',
  `submitdate` datetime NOT NULL,
  `visible` tinyint(1) DEFAULT '0',
  `voteable` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`request_id`)
) DEFAULT CHARSET=utf8;

-- polls
CREATE TABLE `votes` (
  `poll_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `option` int(10) unsigned NOT NULL,
  `date` datetime,
  UNIQUE KEY `UQ_votes` (`poll_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `poll_options` (
  `optid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `poll_id` int(10) unsigned NOT NULL,
  `opttext` varchar(300) NOT NULL,
  PRIMARY KEY (`optid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- alliance news
CREATE TABLE `alliance_creations` (
  `user_id` int(10) unsigned NOT NULL,
  `alliance_id` int(10) unsigned NOT NULL COMMENT 'obsolete, but eh, let''s log this',
  `creationdate` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- auto mark-as-unread user messages
ALTER TABLE `users` ADD COLUMN `user_lastread` INT UNSIGNED NOT NULL DEFAULT 0;

ALTER TABLE `messages` MODIFY COLUMN `is_read` TINYINT(1) NOT NULL DEFAULT 1;