SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `value_types`;
CREATE TABLE `value_types` (
  `id` tinyint NOT NULL,
  `unit` varchar(100) CHARACTER SET utf32 COLLATE utf32_bin NOT NULL COMMENT 'Jednotka',
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
CHANGE `desc` `desc` varchar(100) COLLATE 'utf32_bin' NOT NULL COMMENT 'Popis' AFTER `id`,
COLLATE 'utf32_bin';

ALTER TABLE `sensors`
CHANGE `device_id` `device_id` smallint NOT NULL COMMENT 'Id zariadenia' AFTER `id`,
CHANGE `channel_id` `channel_id` smallint NULL COMMENT 'Id kanálu' AFTER `device_id`,
ADD `warning_icon` tinyint NOT NULL DEFAULT '0' COMMENT 'Upozornenie na chýbajúce dáta',
ADD FOREIGN KEY (`device_id`) REFERENCES `devices` (`id`),
COLLATE 'utf32_bin';

ALTER TABLE `sensors`
CHANGE `id_value_types` `id_value_types` tinyint NOT NULL COMMENT 'Jednotky' AFTER `device_class`,
ADD FOREIGN KEY (`id_value_types`) REFERENCES `value_types` (`id`);

ALTER TABLE `sensors`
CHANGE `name` `name` varchar(100) COLLATE 'utf32_bin' NOT NULL AFTER `channel_id`,
CHANGE `device_class` `id_device_classes` tinyint NOT NULL AFTER `name`,
CHANGE `desc` `desc` varchar(256) COLLATE 'utf32_bin' NULL AFTER `msg_rate`,
CHANGE `data_session` `data_session` varchar(20) COLLATE 'utf32_bin' NULL AFTER `last_out_value`,
CHANGE `warn_max_text` `warn_max_text` varchar(255) COLLATE 'utf32_bin' NULL AFTER `warn_max_val_off`,
CHANGE `warn_min_text` `warn_min_text` varchar(255) COLLATE 'utf32_bin' NULL AFTER `warn_min_val_off`,
ADD FOREIGN KEY (`id_device_classes`) REFERENCES `device_classes` (`id`);