CREATE USER 'uk-mgk'@'localhost' IDENTIFIED BY '***';
CREATE USER 'uk-mgk_admin'@'localhost' IDENTIFIED BY '***';
CREATE DATABASE `uk-mgk` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
GRANT DELETE, INSERT, LOCK TABLES, SELECT, UPDATE ON `uk-mgk` . * TO 'uk-mgk'@'localhost';
GRANT ALL ON `uk-mgk`.* TO 'uk-mgk_admin'@'localhost';