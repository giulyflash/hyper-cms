ALTER TABLE `module_link_param` CHANGE `param_name` `name` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `module_param` CHANGE `param_name` `name` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

CREATE TABLE IF NOT EXISTS `menu_link_alias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `link_template` varchar(255) DEFAULT NULL,
  `alias_template` varchar(255) DEFAULT NULL,
  `order` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

INSERT INTO `menu_link_alias` (`id`, `link_template`, `alias_template`, `order`) VALUES
(1, '/.call=article.get_category&title=([A-Za-z0-9_-]*).*', '/$1/', 1),
(2, '/.call=article.get&title=([A-Za-z0-9_-]*).*', '/$1', 1),
(3, '/.call=article.get_category&title=([A-Za-z0-9_-]*)&(.*)', '/$1/?$2', 1),
(4, '/.call=article.get&title=([A-Za-z0-9_-]*)&(.*)', '/$1?$2', 1),
(5, '/.call=article.get_category', '/article/', 1),
(6, '/.call=article.get', '/article', 1);


INSERT INTO `module_link` (`center_module`, `center_method`, `module_name`, `method_name`, `admin_mode`, `position`, `exclude`, `order`, `inactive`) VALUES
('*', '*', 'breadcrumbs', 'get', NULL, 'breadcrumbs', 0, 1, 0),
('admin_mode.*', '*', 'breadcrumbs', 'get', 1, 'breadcrumbs', 0, 1, 0);

UPDATE module_link SET `method_name` = 'get' WHERE `method_name` = 'get_by_title';

ALTER TABLE `article_category` ADD `article_redirect` VARCHAR( 255 ) NULL DEFAULT NULL;

ALTER TABLE `article` CHANGE `draft` `draft` TINYINT( 2 ) NULL DEFAULT NULL;

ALTER TABLE `article` CHANGE `draft` `draft` TINYINT( 2 ) NOT NULL DEFAULT '0';

ALTER TABLE `article_category` ADD `draft` TINYINT( 2 ) NOT NULL DEFAULT '0';

ALTER TABLE `menu_item` ADD `draft` TINYINT( 2 ) NOT NULL DEFAULT '0';

ALTER TABLE `article_category` DROP `language`;

ALTER TABLE `menu_item` DROP `language`;

ALTER TABLE `article` ADD `category_left` INT NOT NULL;

UPDATE `article` SET `category_left` = (select `left` from article_category where id = category_id), `category_right` = (select `right` from article_category where id = category_id);
ALTER TABLE `article` DROP `category_id`;

ALTER TABLE `article_category` ADD `category_count` INT NULL DEFAULT NULL;

CREATE TEMPORARY TABLE `temp_category` (`left` int(11),`right` int(11));
INSERT INTO temp_category (SELECT `left`,`right` FROM `article_category`);
UPDATE `article_category` SET `category_count` = (SELECT count(*) FROM `temp_category` WHERE `temp_category`.`left`>`article_category`.`left` AND `temp_category`.`right`>`article_category`.`right`);

ALTER TABLE `article` ADD `link` VARCHAR( 255 ) NULL;
ALTER TABLE `article_category` ADD `link` VARCHAR( 255 ) NULL;

UPDATE `article` SET `link`=concat('/',`translit_title`);
UPDATE `article_category` SET `link`=concat('/',`translit_title`,'/');