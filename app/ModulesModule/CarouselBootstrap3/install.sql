
-- First insert Module into list of modules
INSERT INTO page_modules (name, class_name, enabled) 
VALUES ('Prezentace Bootstrap 3', 'ModuleCarouselBootstrap3', 1);

-- Second create needed tables or other stuff in database

SET NAMES utf8;
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

CREATE TABLE IF NOT EXISTS `module_carousel_bootstrap_3` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `page_page_modules_id` int(11) unsigned NOT NULL,
  `image` varchar(255),
  `title` varchar(255),
  `subtitle` varchar(255),
  `text` text,
  `link` text,
  `link_text` varchar(32),
  `order` int(2) NOT NULL DEFAULT 0,
  `enabled` int(1),
  PRIMARY KEY (`id`),
  KEY `page_page_modules_id` (`page_page_modules_id`),
  CONSTRAINT `table_name_ibfk_2` FOREIGN KEY (`page_page_modules_id`) REFERENCES `page_modules_instance` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
