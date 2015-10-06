
-- First insert Module into list of modules
INSERT INTO page_modules (name, class_name, enabled) 
VALUES ('Piktogramy', 'ModulePictograms', 1);

-- Second create needed tables or other stuff in database
-- (Adminer 4.1.0 MySQL dump)

SET NAMES utf8;
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

CREATE TABLE IF NOT EXISTS `module_pictograms` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `page_page_modules_id` int(11) unsigned NOT NULL,
  `enabled` int(1) unsigned NOT NULL,
  `text` text CHARACTER SET utf8,
  `icon` varchar(64) CHARACTER SET utf8 NOT NULL,
  `link` text CHARACTER SET utf8,
  PRIMARY KEY (`id`),
  KEY `page_page_modules_id` (`page_page_modules_id`),
  CONSTRAINT `module_pictograms_ibfk_2` FOREIGN KEY (`page_page_modules_id`) REFERENCES `page_modules_instance` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
