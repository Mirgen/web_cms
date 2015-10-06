
-- First insert Module into list of modules
INSERT INTO page_modules (name, class_name, enabled) 
VALUES ('Vložit kód do stránky', 'ModuleInsertCode', 1);

-- Second create needed tables or other stuff in database

SET NAMES utf8;
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

CREATE TABLE IF NOT EXISTS `module_insert_code` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `page_page_modules_id` int(11) unsigned NOT NULL,
  `code` text CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`),
  KEY `page_page_modules_id` (`page_page_modules_id`),
  CONSTRAINT `module_insert_code_ibfk_2` FOREIGN KEY (`page_page_modules_id`) REFERENCES `page_modules_instance` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
