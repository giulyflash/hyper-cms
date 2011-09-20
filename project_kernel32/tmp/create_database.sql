CREATE USER 'grosstech'@'localhost' IDENTIFIED BY '***';
CREATE USER 'grosstech_admin'@'localhost' IDENTIFIED BY '***';
CREATE DATABASE `grosstech` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
GRANT DELETE, INSERT, LOCK TABLES, SELECT, UPDATE ON `grosstech` . * TO 'grosstech'@'localhost';
GRANT ALL ON `grosstech`.* TO 'grosstech_admin'@'localhost';