SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `modules_settings`;
CREATE TABLE `modules_settings` (
  `module_id` int(11) unsigned NOT NULL,
  `name` varchar(256) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `key` varchar(256) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `value` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  KEY `module_id` (`module_id`),
  CONSTRAINT `modules_settings_ibfk_1` FOREIGN KEY (`module_id`) REFERENCES `page_modules_instance` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `module_carousel_bootstrap_3`;
CREATE TABLE `module_carousel_bootstrap_3` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `page_page_modules_id` int(11) unsigned NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `subtitle` varchar(255) DEFAULT NULL,
  `text` text,
  `link` text,
  `link_text` varchar(32) DEFAULT NULL,
  `order` int(11) NOT NULL DEFAULT '0',
  `enabled` int(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `page_page_modules_id` (`page_page_modules_id`),
  CONSTRAINT `table_name_ibfk_2` FOREIGN KEY (`page_page_modules_id`) REFERENCES `page_modules_instance` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `module_contact_form`;
CREATE TABLE `module_contact_form` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `page_page_modules_id` int(11) unsigned NOT NULL,
  `email` varchar(256) COLLATE utf8_general_ci DEFAULT NULL,
  `message_ok` varchar(256) COLLATE utf8_general_ci NOT NULL DEFAULT 'Email byl odeslán.',
  PRIMARY KEY (`id`),
  KEY `page_page_modules_id` (`page_page_modules_id`),
  CONSTRAINT `module_contact_form_ibfk_1` FOREIGN KEY (`page_page_modules_id`) REFERENCES `page_modules_instance` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `module_email_gatherer`;
CREATE TABLE `module_email_gatherer` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `page_page_modules_id` int(11) unsigned NOT NULL,
  `email` varchar(256) NOT NULL,
  `unsubscribed` int(1) unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `page_page_modules_id` (`page_page_modules_id`),
  CONSTRAINT `module_email_gatherer_ibfk_2` FOREIGN KEY (`page_page_modules_id`) REFERENCES `page_modules_instance` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `module_featuring`;
CREATE TABLE `module_featuring` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `page_page_modules_id` int(11) unsigned NOT NULL,
  `title` varchar(512) COLLATE utf8_general_ci NOT NULL,
  `text` text COLLATE utf8_general_ci NOT NULL,
  `link` varchar(256) COLLATE utf8_general_ci DEFAULT NULL,
  `imagename` varchar(255) COLLATE utf8_general_ci DEFAULT NULL,
  `enabled` int(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `page_page_modules_id` (`page_page_modules_id`),
  CONSTRAINT `module_featuring_ibfk_2` FOREIGN KEY (`page_page_modules_id`) REFERENCES `page_modules_instance` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `module_guest_book_posts`;
CREATE TABLE `module_guest_book_posts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `page_page_modules_id` int(11) unsigned NOT NULL,
  `name` varchar(128) COLLATE utf8_general_ci NOT NULL,
  `email` varchar(256) COLLATE utf8_general_ci NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `text` text COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `page_page_modules_id` (`page_page_modules_id`),
  CONSTRAINT `module_guest_book_posts_ibfk_1` FOREIGN KEY (`page_page_modules_id`) REFERENCES `page_modules_instance` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `module_image_galery_images`;
CREATE TABLE `module_image_galery_images` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `page_page_modules_id` int(11) unsigned NOT NULL,
  `name` varchar(128) COLLATE utf8_general_ci NOT NULL,
  `extension` varchar(4) COLLATE utf8_general_ci DEFAULT NULL,
  `description` text COLLATE utf8_general_ci NOT NULL,
  `order` int(11) NOT NULL DEFAULT '0',
  `enabled` int(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `page_page_modules_id` (`page_page_modules_id`),
  CONSTRAINT `module_image_galery_images_ibfk_1` FOREIGN KEY (`page_page_modules_id`) REFERENCES `page_modules_instance` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `module_insert_code`;
CREATE TABLE `module_insert_code` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `page_page_modules_id` int(11) unsigned NOT NULL,
  `code` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `module_pictograms`;
CREATE TABLE `module_pictograms` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `page_page_modules_id` int(11) unsigned NOT NULL,
  `enabled` int(1) unsigned NOT NULL,
  `text` text CHARACTER SET utf8 COLLATE utf8_general_ci,
  `icon` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `link` text CHARACTER SET utf8 COLLATE utf8_general_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `module_reference`;
CREATE TABLE `module_reference` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `page_page_modules_id` int(11) unsigned NOT NULL,
  `title` varchar(512) COLLATE utf8_general_ci NOT NULL,
  `subtitle` text COLLATE utf8_general_ci,
  `text` text COLLATE utf8_general_ci NOT NULL,
  `client` varchar(512) COLLATE utf8_general_ci DEFAULT NULL,
  `link` varchar(256) COLLATE utf8_general_ci DEFAULT NULL,
  `imagename` varchar(255) COLLATE utf8_general_ci DEFAULT NULL,
  `enabled` int(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `page_page_modules_id` (`page_page_modules_id`),
  CONSTRAINT `module_reference_ibfk_2` FOREIGN KEY (`page_page_modules_id`) REFERENCES `page_modules_instance` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `module_simple_google_maps`;
CREATE TABLE `module_simple_google_maps` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `page_page_modules_id` int(11) unsigned NOT NULL,
  `address` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `module_text_editor`;
CREATE TABLE `module_text_editor` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_page_modules_id` int(11) unsigned NOT NULL,
  `text` text COLLATE utf8_general_ci,
  PRIMARY KEY (`id`),
  KEY `page_page_modules_id` (`page_page_modules_id`),
  CONSTRAINT `module_text_editor_ibfk_3` FOREIGN KEY (`page_page_modules_id`) REFERENCES `page_modules_instance` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `page`;
CREATE TABLE `page` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) COLLATE utf8_general_ci NOT NULL,
  `title` varchar(64) COLLATE utf8_general_ci DEFAULT NULL,
  `seo_url_text` varchar(64) COLLATE utf8_general_ci DEFAULT NULL,
  `final_url_text` text COLLATE utf8_general_ci,
  `id_parent` int(11) unsigned DEFAULT NULL,
  `order` double NOT NULL DEFAULT '0',
  `online` int(1) unsigned NOT NULL DEFAULT '1',
  `deleted` int(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id_parent` (`id_parent`),
  CONSTRAINT `page_id_parent` FOREIGN KEY (`id_parent`) REFERENCES `page` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `page` (`id`, `name`, `title`, `seo_url_text`, `final_url_text`, `id_parent`, `order`, `online`, `deleted`) 
VALUES ('1', 'Úvodní stránka', 'Úvodní stránka', 'Úvodní stránka', 'uvodni-stranka', NULL, '0', '1', '0');

DROP TABLE IF EXISTS `page_modules`;
CREATE TABLE `page_modules` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_general_ci NOT NULL,
  `class_name` varchar(30) COLLATE utf8_general_ci NOT NULL,
  `enabled` int(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `page_modules` (`id`, `name`, `class_name`, `enabled`) VALUES
(1,	'Text',	'ModuleTextEditor',	1),
(2,	'Kontaktní formulář',	'ModuleContactForm',	1),
(3,	'Diskuzní kniha',	'ModuleGuestBook',	1),
(4,	'Obrázková galerie',	'ModuleImageGalery',	1),
(5,	'Reference',	'ModuleReference',	1),
(6,	'Novinky e-mailem (sběr e-mailů)',	'ModuleEmailGatherer',	1),
(7,	'Představujeme',	'ModuleFeaturing',	1),
(8,	'Google mapa',	'ModuleSimpleGoogleMaps',	1),
(9,	'Piktogramy',	'ModulePictograms',	1),
(10,	'Vložit kód do stránky',	'ModuleInsertCode',	1),
(11,	'Prezentace Bootstrap (v.3)',	'ModuleCarouselBootstrap3',	1);
(12,	'Jednoduchý Eshop',	'ModuleSimpleEshop',	1);

DROP TABLE IF EXISTS `page_modules_instance`;
CREATE TABLE `page_modules_instance` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `module_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `page_id` (`module_id`),
  CONSTRAINT `page_modules_instance_ibfk_2` FOREIGN KEY (`module_id`) REFERENCES `page_modules` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `page_modules_presence`;
CREATE TABLE `page_modules_presence` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `page_module_instance_id` int(11) unsigned NOT NULL,
  `page_id` int(11) unsigned NOT NULL,
  `position` int(3) unsigned NOT NULL DEFAULT '1',
  `enabled` int(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `page_id` (`page_id`),
  KEY `page_module_instance_id` (`page_module_instance_id`),
  KEY `page_module_instance_id_2` (`page_module_instance_id`),
  KEY `page_module_instance_id_3` (`page_module_instance_id`),
  CONSTRAINT `page_modules_presence_ibfk_1` FOREIGN KEY (`page_id`) REFERENCES `page` (`id`),
  CONSTRAINT `page_modules_presence_ibfk_2` FOREIGN KEY (`page_module_instance_id`) REFERENCES `page_modules_instance` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `redirections`;
CREATE TABLE `redirections` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `from` varchar(512) COLLATE utf8_general_ci NOT NULL,
  `to` varchar(512) COLLATE utf8_general_ci NOT NULL,
  `code` varchar(3) COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `name` varchar(50) COLLATE utf8_general_ci NOT NULL,
  `value` text COLLATE utf8_general_ci,
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO page_modules (name, class_name, enabled) 
VALUES ('Jednoduchý Eshop', 'ModuleSimpleEshop', 1);

CREATE TABLE IF NOT EXISTS `module_simpleeshop_products` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `page_page_modules_id` int(11) unsigned NOT NULL,
  `title` varchar(128) NOT NULL,
  `description` text,
  `price` double DEFAULT NULL,
  `discount_percentage` int(3) DEFAULT NULL,
  `discount_amount` double DEFAULT NULL,
  `enabled` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `page_page_modules_id` (`page_page_modules_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE `module_simpleeshop_products`
  ADD CONSTRAINT `module_simpleeshop_products_ibfk_1` FOREIGN KEY (`page_page_modules_id`) REFERENCES `page_modules_instance` (`id`) ON DELETE CASCADE;

CREATE TABLE IF NOT EXISTS `module_simpleeshop_images` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(11) unsigned NOT NULL,
  `filename` varchar(128) NOT NULL,
  `file_extension` varchar(4) NOT NULL,
  `main` int(1) NOT NULL DEFAULT '0',
  `enabled` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE `module_simpleeshop_images`
  ADD CONSTRAINT `module_simpleeshop_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `module_simpleeshop_products` (`id`) ON DELETE CASCADE;


CREATE TABLE IF NOT EXISTS `module_simpleeshop_orders` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `page_page_modules_id` int(11) unsigned NOT NULL,
  `name` varchar(256) NOT NULL,
  `surname` varchar(256) NOT NULL,
  `email` varchar(256) NOT NULL,
  `city` varchar(256) NOT NULL,
  `street` varchar(256) NOT NULL,
  `zip_code` varchar(6) NOT NULL,
  `product_id` int(11) unsigned NOT NULL,
  `product_title` varchar(128) NOT NULL,
  `processed` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `page_page_modules_id` (`page_page_modules_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=15 ;

ALTER TABLE `module_simpleeshop_orders`
  ADD CONSTRAINT `module_simpleeshop_orders_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `module_simpleeshop_products` (`id`);