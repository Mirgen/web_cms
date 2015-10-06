
-- First insert Module into list of modules
INSERT INTO page_modules (name, class_name, enabled) 
VALUES ('Představujeme', 'ModuleFeaturing', 1);

-- Second create needed tables or other stuff in database
-- (Adminer 4.1.0 MySQL dump)

SET NAMES utf8;
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

CREATE TABLE IF NOT EXISTS `module_featuring` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `page_page_modules_id` int(11) unsigned NOT NULL,
  `title` varchar(512) COLLATE utf8_czech_ci NOT NULL,
  `text` text COLLATE utf8_czech_ci NOT NULL,
  `link` varchar(256) COLLATE utf8_czech_ci DEFAULT NULL,
  `imagename` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  `enabled` int(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `page_page_modules_id` (`page_page_modules_id`),
  CONSTRAINT `module_featuring_ibfk_2` FOREIGN KEY (`page_page_modules_id`) REFERENCES `page_modules_instance` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
