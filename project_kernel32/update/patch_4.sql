ALTER TABLE `file_category` ADD `draft` BOOLEAN NOT NULL DEFAULT '0';
ALTER TABLE `file_category` ADD `link` VARCHAR( 255 ) NULL DEFAULT NULL ;

ALTER TABLE `file` ADD `draft` BOOLEAN NOT NULL DEFAULT '0';
ALTER TABLE `file` ADD `link` VARCHAR( 255 ) NULL DEFAULT NULL;

CREATE TABLE `question` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `text` tinytext,
  `parent` int(11) DEFAULT NULL,
  `draft` tinyint(1) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `username` varchar(255) NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;