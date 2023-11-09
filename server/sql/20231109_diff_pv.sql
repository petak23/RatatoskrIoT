SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `value_types`;
CREATE TABLE `value_types` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT '[A]Index',
  `unit` varchar(20) CHARACTER SET utf32 COLLATE utf32_bin NOT NULL COMMENT 'Značka jednotky',
  `description` varchar(100) CHARACTER SET utf32 COLLATE utf32_bin DEFAULT NULL COMMENT 'Popis jednotky',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf32 COLLATE=utf32_bin COMMENT='Units for any kind of recorder values.';

INSERT INTO `value_types` (`id`, `unit`, `description`) VALUES
(1,	'°C',	'Teplota'),
(2,	'%',	'Percento | Vlhkosť'),
(3,	'hPa',	'Tlak'),
(4,	'dB',	'Decibely'),
(5,	'ppm',	NULL),
(6,	'kWh',	NULL),
(7,	'#',	NULL),
(8,	'V',	'Napätie'),
(9,	'sec',	NULL),
(10,	'A',	'Prúd'),
(11,	'Ah',	NULL),
(12,	'W',	'Výkon'),
(13,	'Wh',	NULL),
(14,	'mA',	'miliamér'),
(15,	'mAh',	NULL),
(16,	'lx',	'Intenzita svetla'),
(17,	'°',	NULL),
(18,	'm/s',	'Rýchlosť'),
(19,	'mm',	'Milimetre');

ALTER TABLE `sensors`
CHANGE `value_type` `id_value_types` int NOT NULL COMMENT 'Typ jednotky' AFTER `device_class`,
ADD FOREIGN KEY (`id_value_types`) REFERENCES `value_types` (`id`);