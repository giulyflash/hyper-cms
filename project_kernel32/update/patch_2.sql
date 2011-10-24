CREATE TABLE IF NOT EXISTS `file_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `translit_title` varchar(255) NOT NULL,
  `left` int(11) NOT NULL,
  `right` int(11) NOT NULL,
  `depth` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `left` (`left`),
  KEY `right` (`right`),
  KEY `depth` (`depth`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

#ALTER TABLE `article` CHANGE `category_id` `category_name` VARCHAR( 255 ) NULL DEFAULT NULL ;
ALTER TABLE `file` ADD `category_id` INT NULL DEFAULT NULL;

ALTER TABLE `file` CHANGE `order` `order` INT( 11 ) NULL DEFAULT '1';
UPDATE `grosstech`.`file` SET `order` = '1';
ALTER TABLE `file` ADD `thumb2_path` VARCHAR( 255 ) NULL DEFAULT NULL AFTER `thumb_path`;

ALTER TABLE `file` CHANGE `name` `title` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `file` CHANGE `translit_name` `translit_title` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

ALTER TABLE `file` DROP `language`;

ALTER TABLE `menu_item` ADD `translit_title` VARCHAR( 255 ) NULL AFTER `title`;

ALTER TABLE `menu` ADD `translit_title` VARCHAR( 255 ) NULL DEFAULT NULL