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
(0,	'guest',	NULL,	'Bez registrácie'),
(1,	'register',	'guest',	'Registrácia cez web'),
(2,	'passive',	'register',	'Pasívny užívateľ'),
(3,	'active',	'passive',	'Aktívny užívateľ'),
(4,	'manager',	'active',	'Správca obsahu'),
(5,	'admin',	'manager',	'Administrátor');

ALTER TABLE `rausers`
ADD `id_user_roles` int(11) NOT NULL DEFAULT '0' COMMENT 'Rola užívateľa' AFTER `role`,
ADD FOREIGN KEY (`id_user_roles`) REFERENCES `user_roles` (`id`);

UPDATE `rausers` SET `id_user_roles` = '5' WHERE `id` = '1';