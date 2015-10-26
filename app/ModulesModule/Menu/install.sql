
-- First insert Module into list of modules
INSERT INTO page_modules (name, class_name, enabled) 
VALUES ('Menu', 'ModuleMenu', 1);

-- Second create needed tables or other stuff in database

SET NAMES utf8;
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

CREATE TABLE IF NOT EXISTS `module_menu` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) unsigned DEFAULT NULL,
  `page_id` int(11) unsigned DEFAULT NULL,
  `module_id` int(11) unsigned DEFAULT NULL,
  `link` text,
  `link_text` varchar(128) DEFAULT NULL,
  `enabled` int(1) NOT NULL DEFAULT '1',
  `order` int(3) NOT NULL DEFAULT '0',
  `page_page_modules_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `page_id` (`page_id`),
  KEY `id_module` (`module_id`),
  KEY `page_page_modules_id` (`page_page_modules_id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

ALTER TABLE `module_menu`
  ADD CONSTRAINT `module_menu_ibfk_1` FOREIGN KEY (`page_id`) REFERENCES `page` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `module_menu_ibfk_2` FOREIGN KEY (`module_id`) REFERENCES `page_modules_presence` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `module_menu_ibfk_3` FOREIGN KEY (`page_page_modules_id`) REFERENCES `page_modules_instance` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `module_menu_ibfk_4` FOREIGN KEY (`parent_id`) REFERENCES `module_menu` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION;
