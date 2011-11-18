ALTER TABLE `module_link` CHANGE `link_id` `id` INT( 11 ) NOT NULL AUTO_INCREMENT;
UPDATE `module_link` SET `language` = '0';
ALTER TABLE `module_link` CHANGE `language` `inactive` TINYINT( 1 ) NOT NULL DEFAULT '0';
ALTER TABLE `module_link` DROP `instance_id` ;
ALTER TABLE `module_link` ADD `center_method` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '*' AFTER `center_module` ;
ALTER TABLE `module_link` CHANGE `method_name` `method_name` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '*';
ALTER TABLE `module_link` CHANGE `order` `order` INT( 11 ) NOT NULL DEFAULT '1';

CREATE TABLE IF NOT EXISTS `article_category` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1001 ;

ALTER TABLE `menu` DROP `language` ;
#ALTER TABLE `article` ADD `edit_date` DATETIME NULL DEFAULT NULL AFTER `create_date` ;
ALTER TABLE `article` DROP `language` ;
#ALTER TABLE `article` ADD `category_id` INT NULL DEFAULT NULL ;
ALTER TABLE `module_include` DROP `language`;
ALTER TABLE `module_link_param` ADD `type` VARCHAR( 32 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'param';
ALTER TABLE `module_link_param` DROP PRIMARY KEY , ADD PRIMARY KEY ( `link_id` , `param_name` , `type` ) ;
ALTER TABLE `module_link_param` ADD `order` INT NOT NULL;

CREATE TABLE IF NOT EXISTS `position` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `translit_title` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `translit_title` (`translit_title`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

INSERT INTO `position` (`id`, `translit_title`, `title`) VALUES
(1, 'center', 'в центре'),
(2, 'left', 'слева'),
(3, 'right', 'справа'),
(4, 'top', 'сверху');

#ALTER TABLE `file` CHANGE `inner_type` `internal_type` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'file';

RENAME TABLE `menu_category` TO `menu_item` ;

#
#
#

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

ALTER TABLE `file` ADD `category_id` INT NULL DEFAULT NULL;

ALTER TABLE `file` CHANGE `order` `order` INT( 11 ) NULL DEFAULT '1';
UPDATE `file` SET `order` = '1';
ALTER TABLE `file` ADD `thumb2_path` VARCHAR( 255 ) NULL DEFAULT NULL AFTER `thumb_path`;

ALTER TABLE `file` CHANGE `name` `title` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `file` CHANGE `translit_name` `translit_title` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

ALTER TABLE `file` DROP `language`;

ALTER TABLE `menu_item` ADD `translit_title` VARCHAR( 255 ) NULL AFTER `title`;

ALTER TABLE `menu` ADD `translit_title` VARCHAR( 255 ) NULL DEFAULT NULL;

ALTER TABLE `menu_item` ADD `link_id` INT NULL DEFAULT NULL;

#
#
#

DELETE FROM `module_link` WHERE `module_link`.`id` = 1;
INSERT INTO `module_link` (`id`, `center_module`, `center_method`, `module_name`, `method_name`, `admin_mode`, `position`, `exclude`, `order`, `inactive`) VALUES
(1, '*', '*', 'article', 'get_by_title', NULL, 'right', 0, 1, 0);

INSERT INTO `module_link_param` (`link_id`, `param_name`, `value`, `type`, `order`) VALUES
(1, 'show_title', '0', 'param', 1),
(1, 'title', 'Kontaktnaja-informacija', 'param', 1);

update `article` set text=replace(text,'style="width: 263px; height: 91px"', 'class="time"') where translit_title='Kontaktnaja-informacija';