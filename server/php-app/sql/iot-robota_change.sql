SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `user_roles`;
CREATE TABLE `user_roles` (
  `id` int(11) NOT NULL COMMENT 'Index',
  `role` varchar(30) COLLATE utf8_bin NOT NULL DEFAULT 'guest' COMMENT 'Rola pre ACL',
  `inherited` varchar(30) COLLATE utf8_bin DEFAULT NULL COMMENT 'Dedí od roli',
  `name` varchar(30) COLLATE utf8_bin NOT NULL DEFAULT 'Registracia cez web' COMMENT 'Názov úrovne registrácie',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Úrovne registrácie a ich názvy';

INSERT INTO `user_roles` (`id`, `role`, `inherited`, `name`) VALUES
(1,	'guest',	NULL,	'Bez registrácie'),
(2,	'register',	'guest',	'Registrácia cez web'),
(3,	'admin',	'register',	'Administrátor');

-- ALTER TABLE `rausers`
-- ADD `id_user_roles` int(11) NOT NULL DEFAULT '0' COMMENT 'Rola užívateľa' AFTER `role`,
-- ADD FOREIGN KEY (`id_user_roles`) REFERENCES `user_roles` (`id`);

UPDATE `rausers` SET `id_user_roles` = '3' WHERE `id` = '1';

DROP TABLE IF EXISTS `user_resource`;
CREATE TABLE `user_resource` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Index',
  `name` varchar(30) COLLATE utf8_bin NOT NULL COMMENT 'Názov zdroja',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Zdroje oprávnení';

INSERT INTO `user_resource` (`id`, `name`) VALUES
(1,	'Sign'),
(2,	'Homepage'),
(3,	'User'),
(4,	'Crontask'),
(5,	'Device'),
(6,	'Enroll'),
(7,	'Error4xx'),
(8,	'Error'),
(9,	'Gallery'),
(10,	'Chart'),
(11,	'Inventory'),
(12,	'Json'),
(13,	'Monitor'),
(14,	'Ra'),
(15,	'Sensor'),
(16,	'View'),
(17,	'Vitem'),
(18, 'UserAcl');

DROP TABLE IF EXISTS `user_permission`;
CREATE TABLE `user_permission` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Index',
  `id_user_roles` int(11) NOT NULL DEFAULT 0 COMMENT 'Užívateľská rola',
  `id_user_resource` int(11) NOT NULL COMMENT 'Zdroj oprávnenia',
  `actions` varchar(100) COLLATE utf8_bin DEFAULT NULL COMMENT 'Povolenie na akciu. (Ak viac oddelené čiarkou, ak null tak všetko)',
  PRIMARY KEY (`id`),
  KEY `id_user_roles` (`id_user_roles`),
  KEY `id_user_resource` (`id_user_resource`),
  CONSTRAINT `user_permission_ibfk_1` FOREIGN KEY (`id_user_roles`) REFERENCES `user_roles` (`id`),
  CONSTRAINT `user_permission_ibfk_2` FOREIGN KEY (`id_user_resource`) REFERENCES `user_resource` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Užívateľské oprávnenia';

INSERT INTO `user_permission` (`id`, `id_user_roles`, `id_user_resource`, `actions`) VALUES
-- Sign
(1,	1,	1,	NULL), 
-- Homepage
(2,	2,	2,	NULL),
-- User
(3,	3,	3,	NULL),
-- Crontask
(4,	1,	4,	NULL),
-- Device
(5,	1,	5,	'deleteupdate'),
(6,	2,	5,	NULL),
-- Enroll
(7,	1,	6,	NULL),
-- Error4xx
(8,	1,	7,	NULL),
-- Error
(9,	1,	8,	NULL),
-- Gallery
(10,	1,	9,	NULL),
-- Chart
(11,	1,	10,	NULL),
-- Inventory
(12,	2,	11,	NULL),
-- Json
(13,	1,	12,	NULL),
-- Monitor
(14,	1,	13,	NULL),
-- Ra
(15,	1,	14,	NULL),
-- Sensor
(16,	2,	15,	NULL),
-- View
(17,	2,	16,	NULL),
-- Vitem
(18,	2,	17,	NULL),
-- UserAcl
(19,	3,	18,	NULL);


DROP TABLE IF EXISTS `main_menu`;
CREATE TABLE `main_menu` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT '[A]Index',
  `name` varchar(30) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'Zobrazený názov položky',
  `link` varchar(30) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'Odkaz',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_bin COMMENT='Hlavné menu';

INSERT INTO `main_menu` (`id`, `name`, `link`) VALUES
(1,	'Môj účet',	'Inventory:User'),
(2,	'Zariadenia',	'Inventory:Home'),
(3,	'Grafy',	'View:Views'),
(4,	'Kódy jednotiek',	'Inventory:Units'),
(5,	'Uživatelia',	'User:List'),
(6,	'Editácia ACL',	'UserAcl:');