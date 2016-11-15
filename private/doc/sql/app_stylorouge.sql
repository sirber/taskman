DROP TABLE IF EXISTS `file`;
CREATE TABLE `file` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `route` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'ex: /task/view/3',
  `filename` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `content_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `size` int(10) unsigned NOT NULL,
  `nb_download` int(10) unsigned NOT NULL DEFAULT '0',
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `active` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=Aria DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci PAGE_CHECKSUM=1;

INSERT INTO `file` (`id`, `route`, `filename`, `content_type`, `size`, `nb_download`, `description`, `active`) VALUES
(1,	'task/view/4',	'MEGAsyncSetup.exe',	'application/octet-stream',	10644488,	1,	'test 1',	1),
(2,	'task/view/4',	'preview_DSC_7695.jpg',	'image/jpeg',	98749,	0,	'test',	0),
(4,	'task/view/4',	'preview_DSC_1067.jpg',	'image/jpeg',	92314,	0,	'',	0),
(5,	'task/view/4',	'preview_DSC_1061.jpg',	'image/jpeg',	71160,	0,	'test 2',	1);

DROP TABLE IF EXISTS `ref_task_category`;
CREATE TABLE `ref_task_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name_fr` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `name_en` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `active` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=Aria DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci PAGE_CHECKSUM=1;

INSERT INTO `ref_task_category` (`id`, `name_fr`, `name_en`, `active`) VALUES
(1,	'services linguistiques',	'',	1),
(2,	'base de données',	'database',	1),
(3,	'infographie',	'infography',	1),
(4,	'classement et archivage',	'archiving',	1),
(5,	'documents administratifs',	'',	1),
(6,	'suivis de dossier',	'',	1),
(7,	'traitement de données',	'',	1),
(8,	'ressources humaines',	'humain ressources',	1),
(9,	'résolution de problème',	'problem resolution',	1),
(10,	'formation',	'formation',	1),
(11,	'pour les parents',	'for parents',	1),
(12,	'autre',	'other',	1);

DROP TABLE IF EXISTS `task`;
CREATE TABLE `task` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `client_id` int(10) unsigned NOT NULL,
  `category_id` int(10) unsigned NOT NULL,
  `date_start` date NOT NULL,
  `date_end` date NOT NULL,
  `nb_hour_estimated` mediumint(8) unsigned DEFAULT '0',
  `nb_hour_real` mediumint(8) unsigned DEFAULT '0',
  `priority` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `difficulty` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `notes` mediumtext COLLATE utf8_unicode_ci,
  `remuneration_type` tinyint(4) DEFAULT NULL,
  `remuneration_value` mediumint(9) DEFAULT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=Aria DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci PAGE_CHECKSUM=1;

INSERT INTO `task` (`id`, `user_id`, `name`, `client_id`, `category_id`, `date_start`, `date_end`, `nb_hour_estimated`, `nb_hour_real`, `priority`, `difficulty`, `notes`, `remuneration_type`, `remuneration_value`, `active`) VALUES
(4,	1,	'test',	0,	0,	'0000-00-00',	'0000-00-00',	0,	NULL,	2,	1,	'',	0,	0,	1);

DROP TABLE IF EXISTS `task_billing`;
CREATE TABLE `task_billing` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `task_id` int(10) unsigned NOT NULL,
  `type_bill_id` int(10) unsigned NOT NULL,
  `comment` varchar(120) COLLATE utf8_unicode_ci NOT NULL,
  `date` datetime NOT NULL,
  `amount` float unsigned NOT NULL,
  `active` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=Aria DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci PAGE_CHECKSUM=1;


DROP TABLE IF EXISTS `task_marker`;
CREATE TABLE `task_marker` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `task_id` int(10) unsigned NOT NULL,
  `position` smallint(5) unsigned NOT NULL,
  `name` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `comment` varchar(120) COLLATE utf8_unicode_ci NOT NULL,
  `nb_hour_estimated` float unsigned NOT NULL,
  `active` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `task_id` (`task_id`)
) ENGINE=Aria DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci PAGE_CHECKSUM=1;


DROP TABLE IF EXISTS `task_work`;
CREATE TABLE `task_work` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `task_id` int(10) unsigned NOT NULL,
  `date` datetime NOT NULL,
  `comment` varchar(120) COLLATE utf8_unicode_ci NOT NULL,
  `nb_hour_real` float unsigned NOT NULL,
  `active` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=Aria DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci PAGE_CHECKSUM=1;


DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `fullname` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `notes` mediumtext COLLATE utf8_unicode_ci,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `admin` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=Aria DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci PAGE_CHECKSUM=1;

INSERT INTO `user` (`id`, `username`, `password`, `fullname`, `email`, `notes`, `active`, `admin`) VALUES
(1,	'sirber',	'$2y$10$aHBlzsKeyjt07A/HEErCxuUclDisCOLyuBCc21bzwcgvvp9BQxqvG',	'Stéphane Bérubé',	'sirber@hotmail.com',	NULL,	1,	1),
(2,	'karhajee',	'$2y$10$A1WTr280hfPfYA0S5zfSHe5xpzKi7qEeS9iM21WqqAl/USTreLaye',	'Cindy Marceau',	'info@stylorouge.ca',	NULL,	1,	1),
(3,	'test',	'',	'test nom!',	'test@test.com',	NULL,	1,	0);

DROP TABLE IF EXISTS `user_task`;
CREATE TABLE `user_task` (
  `user_id` int(10) unsigned NOT NULL,
  `task_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `user_id_task_id` (`user_id`,`task_id`)
) ENGINE=Aria DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci PAGE_CHECKSUM=1;

INSERT INTO `user_task` (`user_id`, `task_id`) VALUES
(1,	1);

-- 2016-05-26 16:28:01
