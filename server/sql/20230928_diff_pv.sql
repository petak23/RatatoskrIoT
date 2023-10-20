SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `user_permission`;
CREATE TABLE `user_permission` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT 'Index',
  `id_user_roles` int NOT NULL DEFAULT '0' COMMENT 'Užívateľská rola',
  `id_user_resource` int NOT NULL COMMENT 'Zdroj oprávnenia',
  `actions` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL COMMENT 'Povolenie na akciu. (Ak viac oddelené čiarkou, ak null tak všetko)',
  PRIMARY KEY (`id`),
  KEY `id_user_roles` (`id_user_roles`),
  KEY `id_user_resource` (`id_user_resource`),
  CONSTRAINT `user_permission_ibfk_1` FOREIGN KEY (`id_user_roles`) REFERENCES `user_roles` (`id`),
  CONSTRAINT `user_permission_ibfk_2` FOREIGN KEY (`id_user_resource`) REFERENCES `user_resource` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_bin COMMENT='Užívateľské oprávnenia';

INSERT INTO `user_permission` (`id`, `id_user_roles`, `id_user_resource`, `actions`) VALUES
(1,	3,	1,	NULL),
(2,	1,	2,	NULL),
(3,	4,	3,	NULL),
(4,	3,	4,	NULL),
(5,	1,	5,	NULL);

DROP TABLE IF EXISTS `user_resource`;
CREATE TABLE `user_resource` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT 'Index',
  `name` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL COMMENT 'Názov zdroja',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_bin COMMENT='Zdroje oprávnení';

INSERT INTO `user_resource` (`id`, `name`) VALUES
(1,	'Api:Devices'),
(2,	'Api:Units'),
(3,	'Api:Users'),
(4,	'Api:Homepage'),
(5,	'Front:Homepage');

DROP TABLE IF EXISTS `user_roles`;
CREATE TABLE `user_roles` (
  `id` int NOT NULL COMMENT 'Index',
  `role` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL DEFAULT 'guest' COMMENT 'Rola pre ACL',
  `inherited` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL COMMENT 'Dedí od roli',
  `name` varchar(80) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL DEFAULT 'Registracia cez web' COMMENT 'Názov úrovne registrácie',
  `color` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL DEFAULT 'fff' COMMENT 'Farba pozadia',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_bin COMMENT='Úrovne registrácie a ich názvy';

INSERT INTO `user_roles` (`id`, `role`, `inherited`, `name`, `color`) VALUES
(1,	'guest',	NULL,	'Bez registrácie',	'fff'),
(2,	'register',	'guest',	'Registrovaný ale neaktivovaný užívateľ',	'fffc29'),
(3,	'active',	'register',	'Aktivovaný užívateľ',	'7ce300'),
(4,	'admin',	'active',	'Administrátor',	'ff6a6a');

ALTER TABLE `rausers`
ADD `id_user_roles` int NOT NULL DEFAULT '1' COMMENT 'Rola užívateľa' AFTER `role`,
ADD FOREIGN KEY (`id_user_roles`) REFERENCES `user_roles` (`id`);

UPDATE `rausers` SET `id_user_roles` = '4' WHERE `id` = '1';

ALTER TABLE `sensors`
ADD `warning_icon` tinyint NOT NULL DEFAULT '0' COMMENT 'Upozornenie na chýbajúce dáta';