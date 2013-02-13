CREATE USER 'uk-mgk'@'localhost' IDENTIFIED BY '***';
CREATE USER 'uk-mgk_admin'@'localhost' IDENTIFIED BY '***';
CREATE DATABASE `uk-mgk` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
GRANT DELETE, INSERT, LOCK TABLES, SELECT, UPDATE ON `uk-mgk` . * TO 'uk-mgk'@'localhost';
GRANT ALL ON `uk-mgk`.* TO 'uk-mgk_admin'@'localhost';

CREATE USER 'jkomfort'@'localhost' IDENTIFIED BY '***';
CREATE DATABASE `jkomfort` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
GRANT ALTER, CREATE, DELETE, DROP, INSERT, LOCK TABLES, SELECT, UPDATE ON `jkomfort` . * TO 'jkomfort'@'localhost';

CREATE DATABASE `jkomfort_apatity` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
GRANT ALTER, CREATE, DELETE, DROP, INSERT, LOCK TABLES, SELECT, UPDATE ON `jkomfort_apatity` . * TO 'jkomfort'@'localhost';

CREATE DATABASE `jkomfort_olenegorsk` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
GRANT ALTER, CREATE, DELETE, DROP, INSERT, LOCK TABLES, SELECT, UPDATE ON `jkomfort_olenegorsk` . * TO 'jkomfort'@'localhost';

CREATE DATABASE `jkomfort_zelenoborskiy` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
GRANT ALTER, CREATE, DELETE, DROP, INSERT, LOCK TABLES, SELECT, UPDATE ON `jkomfort_zelenoborskiy` . * TO 'jkomfort'@'localhost';

CREATE DATABASE `jkomfort_kandalaksha` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
GRANT ALTER, CREATE, DELETE, DROP, INSERT, LOCK TABLES, SELECT, UPDATE ON `jkomfort_kandalaksha` . * TO 'jkomfort'@'localhost';

CREATE DATABASE `jkomfort_nordservis` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
GRANT ALTER, CREATE, DELETE, DROP, INSERT, LOCK TABLES, SELECT, UPDATE ON `jkomfort_nordservis` . * TO 'jkomfort'@'localhost';

CREATE DATABASE `jkomfort_komfortplus` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
GRANT ALTER, CREATE, DELETE, DROP, INSERT, LOCK TABLES, SELECT, UPDATE ON `jkomfort_komfortplus` . * TO 'jkomfort'@'localhost';

CREATE DATABASE `jkomfort_upravdom51` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
CREATE USER 'upravdom51'@'localhost' IDENTIFIED BY 'e12b3bd894';
GRANT ALTER, CREATE, DELETE, DROP, INSERT, LOCK TABLES, SELECT, UPDATE ON `jkomfort_upravdom51` . * TO 'upravdom51'@'localhost';