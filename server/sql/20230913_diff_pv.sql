SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `value_types`;
CREATE TABLE `value_types` (
  `id` tinyint NOT NULL,
  `unit` varchar(25) CHARACTER SET utf32 COLLATE utf32_bin NOT NULL COMMENT 'Jednotka',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf32 COLLATE=utf32_bin COMMENT='Units for any kind of recorder values.';

INSERT INTO `value_types` (`id`, `unit`) VALUES
(1,	'°C'),
(2,	'%'),
(3,	'hPa'),
(4,	'dB'),
(5,	'ppm'),
(6,	'kWh'),
(7,	'#'),
(8,	'V'),
(9,	'sec'),
(10,	'A'),
(11,	'Ah'),
(12,	'W'),
(13,	'Wh'),
(14,	'mA'),
(15,	'mAh'),
(16,	'lx'),
(17,	'°'),
(18,	'm/s'),
(19,	'mm');

ALTER TABLE `device_classes`
CHANGE `id` `id` tinyint NOT NULL COMMENT '[A]Index' AUTO_INCREMENT PRIMARY KEY FIRST,
CHANGE `desc` `desc` varchar(50) COLLATE 'utf32_bin' NOT NULL COMMENT 'Popis' AFTER `id`,
COLLATE 'utf32_bin';

ALTER TABLE `sensors`
CHANGE `device_id` `device_id` smallint NOT NULL COMMENT 'Id zariadenia' AFTER `id`,
CHANGE `channel_id` `channel_id` smallint NULL COMMENT 'Id kanálu' AFTER `device_id`,
ADD `warning_icon` tinyint NOT NULL DEFAULT '0' COMMENT 'Upozornenie na chýbajúce dáta',
ADD FOREIGN KEY (`device_id`) REFERENCES `devices` (`id`),
COLLATE 'utf32_bin';