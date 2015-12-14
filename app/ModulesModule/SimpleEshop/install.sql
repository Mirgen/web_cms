
-- First insert Module into list of modules
INSERT INTO page_modules (name, class_name, enabled) 
VALUES ('Jednoduchý Eshop', 'ModuleSimpleEshop', 1);

-- Second create needed tables or other stuff in database

SET NAMES utf8;
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

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
