CREATE TABLE `grosstech`.`question` (
	`id` INT( 255 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`text` VARCHAR( 255 ) NULL DEFAULT NULL ,
	`create_date` DATETIME NULL DEFAULT NULL ,
	`owner` VARCHAR( 255 ) NULL DEFAULT NULL ,
	`edit_date` DATETIME NULL DEFAULT NULL ,
	`draft` INT NULL DEFAULT NULL
) ENGINE = InnoDB;