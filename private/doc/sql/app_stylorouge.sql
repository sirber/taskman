-- Adminer 4.2.3 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=Aria AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci PAGE_CHECKSUM=1;

INSERT INTO `user` (`id`, `username`, `password`, `email`) VALUES
(1,	'sirber',	'$2y$10$aHBlzsKeyjt07A/HEErCxuUclDisCOLyuBCc21bzwcgvvp9BQxqvG',	'sirber@hotmail.com');

-- 2016-02-02 18:09:51
