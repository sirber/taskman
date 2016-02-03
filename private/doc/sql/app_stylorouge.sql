-- Adminer 4.2.3 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP DATABASE IF EXISTS `app_stylorouge`;
CREATE DATABASE `app_stylorouge` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci */;
USE `app_stylorouge`;

DROP TABLE IF EXISTS `task`;
CREATE TABLE `task` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `date_start` date NOT NULL,
  `date_end` date NOT NULL,
  `nb_hour_estimated` mediumint(8) unsigned NOT NULL,
  `nb_hour_real` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=Aria AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci PAGE_CHECKSUM=1;

INSERT INTO `task` (`id`, `name`, `date_start`, `date_end`, `nb_hour_estimated`, `nb_hour_real`) VALUES
(1,	'test',	'2016-02-02',	'2026-02-05',	3,	0);

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `fullname` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `admin` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=Aria AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci PAGE_CHECKSUM=1;

INSERT INTO `user` (`id`, `username`, `password`, `fullname`, `email`, `active`, `admin`) VALUES
(1,	'sirber',	'$2y$10$aHBlzsKeyjt07A/HEErCxuUclDisCOLyuBCc21bzwcgvvp9BQxqvG',	'Stéphane Bérubé',	'sirber@hotmail.com',	1,	1);

DROP TABLE IF EXISTS `user_task`;
CREATE TABLE `user_task` (
  `user_id` int(10) unsigned NOT NULL,
  `task_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `user_id_task_id` (`user_id`,`task_id`)
) ENGINE=Aria DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci PAGE_CHECKSUM=1;

INSERT INTO `user_task` (`user_id`, `task_id`) VALUES
(1,	1);

-- 2016-02-03 17:09:39