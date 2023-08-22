-- Adminer 4.8.1 MySQL 10.3.32-MariaDB-log dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `blobs`;
CREATE TABLE `blobs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `device_id` smallint(6) NOT NULL,
  `data_time` datetime NOT NULL,
  `server_time` datetime NOT NULL,
  `description` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `extension` varchar(50) COLLATE utf8_czech_ci NOT NULL,
  `filename` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  `session_id` mediumint(9) DEFAULT NULL,
  `remote_ip` varchar(32) COLLATE utf8_czech_ci DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '1 = nahrano, 2 = zpracovano cron taskem (jen obrazky jpg), 3 = exportovano (jen obrazky jpg)',
  `filesize` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `devices`;
CREATE TABLE `devices` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `passphrase` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `name` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `desc` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  `first_login` datetime DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `last_bad_login` datetime DEFAULT NULL,
  `user_id` smallint(6) NOT NULL,
  `json_token` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  `blob_token` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  `monitoring` tinyint(4) DEFAULT NULL,
  `app_name` varchar(256) COLLATE utf8_czech_ci DEFAULT NULL,
  `uptime` int(11) DEFAULT NULL,
  `rssi` smallint(6) DEFAULT NULL,
  `config_ver` smallint(6) DEFAULT NULL,
  `config_data` text COLLATE utf8_czech_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='List of devices. Device has one or more Sensors.';

INSERT INTO `devices` (`id`, `passphrase`, `name`, `desc`, `first_login`, `last_login`, `last_bad_login`, `user_id`, `json_token`, `blob_token`, `monitoring`, `app_name`, `uptime`, `rssi`, `config_ver`, `config_data`) VALUES
(1,	'3a3a53cdc87d69ce5fa0dc3c838d4d53',	'PV:meteozahradka',	'Meteorologická stanica na záhradke',	NULL,	NULL,	NULL,	1,	'a746p2vo4pikwb1a2euvo1ofeaj6ypr20wqpvs64',	'zhaq8xz0amigbxblmflcbpssp8esv9hw7hwub2p5',	1,	NULL,	NULL,	NULL,	NULL,	NULL),
(2,	'2bc0775814adefe3af97f10a83e6ecb4',	'PV:meteobalkon',	'Pokusné meranie na balkóne',	'2023-08-17 14:10:20',	'2023-08-17 14:45:28',	NULL,	1,	'7570sy45g0ifiqvrma71glj3g2zwfyr7ca1f4oef',	'loxe7c3578wcdb6we0g6lcvd5febgctt9jsuha4e',	1,	'[Meteo_50a_01]; /home/petak23/arduino/arduino/v50a-BME280-meteomini/v50a-BME280-meteomini.ino, Aug 17 2023 14:40:20; RA 5.4.1; LS Y; OTA Y; ESP32',	423,	-51,	NULL,	NULL);

DROP TABLE IF EXISTS `device_classes`;
CREATE TABLE `device_classes` (
  `id` int(11) NOT NULL,
  `desc` varchar(100) COLLATE utf8_czech_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `device_classes` (`id`, `desc`) VALUES
(1,	'CONTINUOUS_MINMAXAVG'),
(2,	'CONTINUOUS'),
(3,	'IMPULSE_SUM');

DROP TABLE IF EXISTS `measures`;
CREATE TABLE `measures` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sensor_id` smallint(6) NOT NULL,
  `data_time` datetime NOT NULL COMMENT 'timestamp of data recording',
  `server_time` datetime NOT NULL COMMENT 'timestamp where data has been received by server',
  `s_value` double NOT NULL COMMENT 'data measured (raw)',
  `session_id` mediumint(9) DEFAULT NULL,
  `remote_ip` varchar(32) COLLATE utf8_czech_ci DEFAULT NULL,
  `out_value` double DEFAULT NULL COMMENT 'processed value',
  `status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0 = received, 1 = processed, 2 = exported',
  PRIMARY KEY (`id`),
  KEY `device_id_sensor_id_data_time_id` (`sensor_id`,`data_time`,`id`),
  KEY `status_id` (`status`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Recorded data - raw. SUMDATA are created from recorded data, and old data are deleted from MEASURES.';

INSERT INTO `measures` (`id`, `sensor_id`, `data_time`, `server_time`, `s_value`, `session_id`, `remote_ip`, `out_value`, `status`) VALUES
(1,	1,	'2023-08-17 14:10:19',	'2023-08-17 14:10:20',	24.2,	1,	'188.112.85.173',	24.2,	0),
(2,	2,	'2023-08-17 14:10:19',	'2023-08-17 14:10:20',	57.23828,	1,	'188.112.85.173',	57.23828,	0),
(3,	3,	'2023-08-17 14:10:19',	'2023-08-17 14:10:20',	1025.785,	1,	'188.112.85.173',	1025.785,	0),
(4,	1,	'2023-08-17 14:11:18',	'2023-08-17 14:11:18',	24.32,	1,	'188.112.85.173',	24.32,	0),
(5,	2,	'2023-08-17 14:11:18',	'2023-08-17 14:11:18',	57.22949,	1,	'188.112.85.173',	57.22949,	0),
(6,	3,	'2023-08-17 14:11:18',	'2023-08-17 14:11:18',	1025.77,	1,	'188.112.85.173',	1025.77,	0),
(7,	1,	'2023-08-17 14:12:18',	'2023-08-17 14:12:18',	24.32,	1,	'188.112.85.173',	24.32,	0),
(8,	2,	'2023-08-17 14:12:18',	'2023-08-17 14:12:18',	57.22949,	1,	'188.112.85.173',	57.22949,	0),
(9,	3,	'2023-08-17 14:12:18',	'2023-08-17 14:12:18',	1025.77,	1,	'188.112.85.173',	1025.77,	0),
(10,	1,	'2023-08-17 14:13:18',	'2023-08-17 14:13:18',	24.32,	1,	'188.112.85.173',	24.32,	0),
(11,	2,	'2023-08-17 14:13:18',	'2023-08-17 14:13:18',	57.22949,	1,	'188.112.85.173',	57.22949,	0),
(12,	3,	'2023-08-17 14:13:18',	'2023-08-17 14:13:18',	1025.77,	1,	'188.112.85.173',	1025.77,	0),
(13,	1,	'2023-08-17 14:14:19',	'2023-08-17 14:14:19',	24.32,	1,	'188.112.85.173',	24.32,	0),
(14,	2,	'2023-08-17 14:14:19',	'2023-08-17 14:14:19',	57.22949,	1,	'188.112.85.173',	57.22949,	0),
(15,	3,	'2023-08-17 14:14:19',	'2023-08-17 14:14:19',	1025.77,	1,	'188.112.85.173',	1025.77,	0),
(16,	1,	'2023-08-17 14:15:18',	'2023-08-17 14:15:18',	24.32,	1,	'188.112.85.173',	24.32,	0),
(17,	2,	'2023-08-17 14:15:18',	'2023-08-17 14:15:18',	57.22949,	1,	'188.112.85.173',	57.22949,	0),
(18,	3,	'2023-08-17 14:15:19',	'2023-08-17 14:15:19',	1025.77,	1,	'188.112.85.173',	1025.77,	0),
(19,	1,	'2023-08-17 14:16:18',	'2023-08-17 14:16:18',	24.32,	1,	'188.112.85.173',	24.32,	0),
(20,	2,	'2023-08-17 14:16:18',	'2023-08-17 14:16:18',	57.22949,	1,	'188.112.85.173',	57.22949,	0),
(21,	3,	'2023-08-17 14:16:18',	'2023-08-17 14:16:18',	1025.77,	1,	'188.112.85.173',	1025.77,	0),
(22,	1,	'2023-08-17 14:17:18',	'2023-08-17 14:17:18',	24.32,	1,	'188.112.85.173',	24.32,	0),
(23,	2,	'2023-08-17 14:17:18',	'2023-08-17 14:17:18',	57.22949,	1,	'188.112.85.173',	57.22949,	0),
(24,	3,	'2023-08-17 14:17:18',	'2023-08-17 14:17:18',	1025.77,	1,	'188.112.85.173',	1025.77,	0),
(25,	1,	'2023-08-17 14:18:18',	'2023-08-17 14:18:18',	24.32,	1,	'188.112.85.173',	24.32,	0),
(26,	2,	'2023-08-17 14:18:18',	'2023-08-17 14:18:18',	57.22949,	1,	'188.112.85.173',	57.22949,	0),
(27,	3,	'2023-08-17 14:18:18',	'2023-08-17 14:18:18',	1025.77,	1,	'188.112.85.173',	1025.77,	0),
(28,	1,	'2023-08-17 14:19:19',	'2023-08-17 14:19:19',	24.32,	1,	'188.112.85.173',	24.32,	0),
(29,	2,	'2023-08-17 14:19:19',	'2023-08-17 14:19:19',	57.22949,	1,	'188.112.85.173',	57.22949,	0),
(30,	3,	'2023-08-17 14:19:19',	'2023-08-17 14:19:19',	1025.77,	1,	'188.112.85.173',	1025.77,	0),
(31,	1,	'2023-08-17 14:20:18',	'2023-08-17 14:20:18',	24.32,	1,	'188.112.85.173',	24.32,	0),
(32,	2,	'2023-08-17 14:20:18',	'2023-08-17 14:20:18',	57.22949,	1,	'188.112.85.173',	57.22949,	0),
(33,	3,	'2023-08-17 14:20:18',	'2023-08-17 14:20:18',	1025.77,	1,	'188.112.85.173',	1025.77,	0),
(34,	1,	'2023-08-17 14:21:18',	'2023-08-17 14:21:18',	24.32,	1,	'188.112.85.173',	24.32,	0),
(35,	2,	'2023-08-17 14:21:18',	'2023-08-17 14:21:18',	57.22949,	1,	'188.112.85.173',	57.22949,	0),
(36,	3,	'2023-08-17 14:21:18',	'2023-08-17 14:21:18',	1025.77,	1,	'188.112.85.173',	1025.77,	0),
(37,	1,	'2023-08-17 14:22:18',	'2023-08-17 14:22:18',	24.32,	1,	'188.112.85.173',	24.32,	0),
(38,	2,	'2023-08-17 14:22:18',	'2023-08-17 14:22:18',	57.22949,	1,	'188.112.85.173',	57.22949,	0),
(39,	3,	'2023-08-17 14:22:18',	'2023-08-17 14:22:18',	1025.77,	1,	'188.112.85.173',	1025.77,	0),
(40,	1,	'2023-08-17 14:44:54',	'2023-08-17 14:44:54',	24.46,	2,	'188.112.85.173',	24.46,	0),
(41,	2,	'2023-08-17 14:44:54',	'2023-08-17 14:44:54',	56.64746,	2,	'188.112.85.173',	56.64746,	0),
(42,	3,	'2023-08-17 14:44:54',	'2023-08-17 14:44:54',	1026.038,	2,	'188.112.85.173',	1026.038,	0),
(43,	1,	'2023-08-17 14:45:31',	'2023-08-17 14:45:32',	24.46,	3,	'188.112.85.173',	24.46,	0),
(44,	2,	'2023-08-17 14:45:31',	'2023-08-17 14:45:32',	57.49121,	3,	'188.112.85.173',	57.49121,	0),
(45,	3,	'2023-08-17 14:45:31',	'2023-08-17 14:45:32',	1025.852,	3,	'188.112.85.173',	1025.852,	0),
(46,	1,	'2023-08-17 14:46:27',	'2023-08-17 14:46:27',	24.58,	3,	'188.112.85.173',	24.58,	0),
(47,	2,	'2023-08-17 14:46:27',	'2023-08-17 14:46:27',	57.49316,	3,	'188.112.85.173',	57.49316,	0),
(48,	3,	'2023-08-17 14:46:27',	'2023-08-17 14:46:27',	1025.864,	3,	'188.112.85.173',	1025.864,	0),
(49,	1,	'2023-08-17 14:47:29',	'2023-08-17 14:47:29',	24.58,	3,	'188.112.85.173',	24.58,	0),
(50,	2,	'2023-08-17 14:47:29',	'2023-08-17 14:47:29',	57.49316,	3,	'188.112.85.173',	57.49316,	0),
(51,	3,	'2023-08-17 14:47:29',	'2023-08-17 14:47:29',	1025.864,	3,	'188.112.85.173',	1025.864,	0),
(52,	1,	'2023-08-17 14:48:27',	'2023-08-17 14:48:27',	24.58,	3,	'188.112.85.173',	24.58,	0),
(53,	2,	'2023-08-17 14:48:27',	'2023-08-17 14:48:27',	57.49316,	3,	'188.112.85.173',	57.49316,	0),
(54,	3,	'2023-08-17 14:48:27',	'2023-08-17 14:48:27',	1025.864,	3,	'188.112.85.173',	1025.864,	0),
(55,	1,	'2023-08-17 14:49:27',	'2023-08-17 14:49:27',	24.58,	3,	'188.112.85.173',	24.58,	0),
(56,	2,	'2023-08-17 14:49:27',	'2023-08-17 14:49:27',	57.49316,	3,	'188.112.85.173',	57.49316,	0),
(57,	3,	'2023-08-17 14:49:27',	'2023-08-17 14:49:27',	1025.864,	3,	'188.112.85.173',	1025.864,	0),
(58,	1,	'2023-08-17 14:50:27',	'2023-08-17 14:50:27',	24.58,	3,	'188.112.85.173',	24.58,	0),
(59,	2,	'2023-08-17 14:50:27',	'2023-08-17 14:50:27',	57.49316,	3,	'188.112.85.173',	57.49316,	0),
(60,	3,	'2023-08-17 14:50:27',	'2023-08-17 14:50:27',	1025.864,	3,	'188.112.85.173',	1025.864,	0),
(61,	1,	'2023-08-17 14:51:27',	'2023-08-17 14:51:27',	24.58,	3,	'188.112.85.173',	24.58,	0),
(62,	2,	'2023-08-17 14:51:27',	'2023-08-17 14:51:27',	57.49316,	3,	'188.112.85.173',	57.49316,	0),
(63,	3,	'2023-08-17 14:51:27',	'2023-08-17 14:51:27',	1025.864,	3,	'188.112.85.173',	1025.864,	0),
(64,	1,	'2023-08-17 14:52:27',	'2023-08-17 14:52:27',	24.58,	3,	'188.112.85.173',	24.58,	0),
(65,	2,	'2023-08-17 14:52:27',	'2023-08-17 14:52:27',	57.49316,	3,	'188.112.85.173',	57.49316,	0),
(66,	3,	'2023-08-17 14:52:27',	'2023-08-17 14:52:27',	1025.864,	3,	'188.112.85.173',	1025.864,	0);

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rauser_id` int(11) DEFAULT NULL,
  `device_id` int(11) DEFAULT NULL,
  `sensor_id` int(11) DEFAULT NULL,
  `event_type` tinyint(4) NOT NULL COMMENT '1 sensor max, 2 sensor min, 3 device se nepripojuje, 4 senzor neposila data',
  `event_ts` datetime NOT NULL,
  `status` int(11) NOT NULL DEFAULT 0 COMMENT '0 vygenerováno, 1 odeslán mail',
  `custom_text` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  `out_value` double DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `prelogin`;
CREATE TABLE `prelogin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hash` varchar(20) COLLATE utf8_czech_ci NOT NULL,
  `device_id` smallint(6) NOT NULL,
  `started` datetime NOT NULL,
  `remote_ip` varchar(32) COLLATE utf8_czech_ci NOT NULL,
  `session_key` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Sem se ukládají session po akci LOGINA - před tím, než je zařízení potvrdí via LOGINB';


DROP TABLE IF EXISTS `rausers`;
CREATE TABLE `rausers` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8_bin NOT NULL,
  `phash` varchar(255) COLLATE utf8_bin NOT NULL,
  `role` varchar(100) COLLATE utf8_bin NOT NULL,
  `email` varchar(255) COLLATE utf8_bin NOT NULL,
  `prefix` varchar(20) COLLATE utf8_bin NOT NULL,
  `state_id` tinyint(4) NOT NULL DEFAULT 10,
  `bad_pwds_count` smallint(6) NOT NULL DEFAULT 0,
  `locked_out_until` datetime DEFAULT NULL,
  `measures_retention` int(11) NOT NULL DEFAULT 90 COMMENT 'jak dlouho se drží data v measures',
  `sumdata_retention` int(11) NOT NULL DEFAULT 731 COMMENT 'jak dlouho se drží data v sumdata',
  `blob_retention` int(11) NOT NULL DEFAULT 14 COMMENT 'jak dlouho se drží bloby',
  `self_enroll` tinyint(4) NOT NULL DEFAULT 0 COMMENT '1 = self-enrolled',
  `self_enroll_code` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `self_enroll_error_count` tinyint(4) DEFAULT 0,
  `cur_login_time` datetime DEFAULT NULL,
  `cur_login_ip` varchar(32) COLLATE utf8_bin DEFAULT NULL,
  `cur_login_browser` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `prev_login_time` datetime DEFAULT NULL,
  `prev_login_ip` varchar(32) COLLATE utf8_bin DEFAULT NULL,
  `prev_login_browser` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `last_error_time` datetime DEFAULT NULL,
  `last_error_ip` varchar(32) COLLATE utf8_bin DEFAULT NULL,
  `last_error_browser` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `monitoring_token` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO `rausers` (`id`, `username`, `phash`, `role`, `email`, `prefix`, `state_id`, `bad_pwds_count`, `locked_out_until`, `measures_retention`, `sumdata_retention`, `blob_retention`, `self_enroll`, `self_enroll_code`, `self_enroll_error_count`, `cur_login_time`, `cur_login_ip`, `cur_login_browser`, `prev_login_time`, `prev_login_ip`, `prev_login_browser`, `last_error_time`, `last_error_ip`, `last_error_browser`, `monitoring_token`) VALUES
(1,	'admin',	'$2y$11$hPoH7YqgeuCpOkUVXbOMTes7QEOUHw542uou7TdEBDrUsf/LRldKW',	'admin,user',	'petak23@echo-msz.eu',	'PV',	10,	0,	'2023-07-31 13:59:27',	90,	731,	14,	0,	NULL,	0,	'2023-08-17 14:21:01',	'188.112.85.173',	'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:109.0) Gecko/20100101 Firefox/115.0 / sk,cs;q=0.8,en-US;q=0.5,en;q=0.3',	'2023-08-17 12:26:23',	'188.112.101.13',	'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:109.0) Gecko/20100101 Firefox/115.0 / sk,cs;q=0.8,en-US;q=0.5,en;q=0.3',	'2023-07-31 13:59:11',	'127.0.0.1',	'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:109.0) Gecko/20100101 Firefox/115.0 / sk,cs;q=0.8,en-US;q=0.5,en;q=0.3',	NULL);

DROP TABLE IF EXISTS `rauser_state`;
CREATE TABLE `rauser_state` (
  `id` tinyint(4) NOT NULL,
  `desc` varchar(100) COLLATE utf8_czech_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `rauser_state` (`id`, `desc`) VALUES
(1,	'čeká na zadání kódu z e-mailu'),
(10,	'aktivní'),
(90,	'zakázán administrátorem'),
(91,	'dočasně uzamčen');

DROP TABLE IF EXISTS `sensors`;
CREATE TABLE `sensors` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `device_id` smallint(6) NOT NULL,
  `channel_id` smallint(6) DEFAULT NULL,
  `name` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `device_class` tinyint(4) NOT NULL,
  `value_type` tinyint(4) NOT NULL,
  `msg_rate` int(11) NOT NULL COMMENT 'expected delay between messages',
  `desc` varchar(256) COLLATE utf8_czech_ci DEFAULT NULL,
  `display_nodata_interval` int(11) NOT NULL DEFAULT 7200 COMMENT 'how long interval will be detected as "no data"',
  `preprocess_data` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0 = no, 1 = yes',
  `preprocess_factor` double DEFAULT NULL COMMENT 'out = factor * sensor_data',
  `last_data_time` datetime DEFAULT NULL,
  `last_out_value` double DEFAULT NULL,
  `data_session` varchar(20) COLLATE utf8_czech_ci DEFAULT NULL,
  `imp_count` bigint(20) DEFAULT NULL,
  `warn_max` tinyint(4) NOT NULL DEFAULT 0,
  `warn_max_after` int(11) NOT NULL DEFAULT 0 COMMENT 'za jak dlouho se má poslat',
  `warn_max_val` double DEFAULT NULL,
  `warn_max_val_off` double DEFAULT NULL COMMENT 'vypínací hodnota',
  `warn_max_text` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  `warn_max_fired` datetime DEFAULT NULL,
  `warn_max_sent` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0 = ne, 1 = posláno',
  `warn_min` tinyint(4) NOT NULL DEFAULT 0,
  `warn_min_after` int(11) NOT NULL DEFAULT 0 COMMENT 'za jak dlouho se má poslat',
  `warn_min_val` double DEFAULT NULL,
  `warn_min_val_off` double DEFAULT NULL COMMENT 'vypínací hodnota',
  `warn_min_text` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  `warn_min_fired` datetime DEFAULT NULL,
  `warn_min_sent` tinyint(4) DEFAULT 0 COMMENT '0 = ne, 1 = posláno',
  `warn_noaction_fired` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `device_id_name` (`device_id`,`name`),
  KEY `device_id_channel_id_name` (`device_id`,`channel_id`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='List of sensors. Each sensor is a part of one DEVICE.';

INSERT INTO `sensors` (`id`, `device_id`, `channel_id`, `name`, `device_class`, `value_type`, `msg_rate`, `desc`, `display_nodata_interval`, `preprocess_data`, `preprocess_factor`, `last_data_time`, `last_out_value`, `data_session`, `imp_count`, `warn_max`, `warn_max_after`, `warn_max_val`, `warn_max_val_off`, `warn_max_text`, `warn_max_fired`, `warn_max_sent`, `warn_min`, `warn_min_after`, `warn_min_val`, `warn_min_val_off`, `warn_min_text`, `warn_min_fired`, `warn_min_sent`, `warn_noaction_fired`) VALUES
(1,	2,	1,	'temperature',	1,	1,	3600,	'temperature',	7200,	0,	NULL,	'2023-08-17 14:52:27',	24.58,	NULL,	NULL,	0,	0,	NULL,	NULL,	NULL,	NULL,	0,	0,	0,	NULL,	NULL,	NULL,	NULL,	0,	NULL),
(2,	2,	2,	'humidity',	1,	2,	3600,	'humidity',	7200,	0,	NULL,	'2023-08-17 14:52:27',	57.49316,	NULL,	NULL,	0,	0,	NULL,	NULL,	NULL,	NULL,	0,	0,	0,	NULL,	NULL,	NULL,	NULL,	0,	NULL),
(3,	2,	3,	'presure',	1,	3,	3600,	'presure',	7200,	0,	NULL,	'2023-08-17 14:52:27',	1025.864,	NULL,	NULL,	0,	0,	NULL,	NULL,	NULL,	NULL,	0,	0,	0,	NULL,	NULL,	NULL,	NULL,	0,	NULL);

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `hash` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `device_id` smallint(6) NOT NULL,
  `started` datetime NOT NULL,
  `remote_ip` varchar(32) NOT NULL,
  `session_key` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `device_id` (`device_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Sessions on IoT interface.';

INSERT INTO `sessions` (`id`, `hash`, `device_id`, `started`, `remote_ip`, `session_key`) VALUES
(3,	'sEpjeUnX',	2,	'2023-08-17 14:45:28',	'188.112.85.173',	'96d7aa7ae4b37373e36bed0417f37bcd4d5efe1d283fea244e04e121c143e759');

DROP TABLE IF EXISTS `sumdata`;
CREATE TABLE `sumdata` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sensor_id` smallint(6) NOT NULL,
  `sum_type` tinyint(4) NOT NULL COMMENT '1 = hour, 2 = day',
  `rec_date` date NOT NULL,
  `rec_hour` tinyint(4) NOT NULL COMMENT '-1 if day value',
  `min_val` double DEFAULT NULL,
  `min_time` time DEFAULT NULL,
  `max_val` double DEFAULT NULL,
  `max_time` time DEFAULT NULL,
  `avg_val` double DEFAULT NULL,
  `sum_val` double DEFAULT NULL,
  `ct_val` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Počet započtených hodnot (pro denní sumy)',
  `status` tinyint(4) DEFAULT 0 COMMENT '0 = created hourly stat (= daily stat should be recomputed), 1 = used',
  PRIMARY KEY (`id`),
  KEY `sensor_id_rec_date_sum_type` (`sensor_id`,`rec_date`,`sum_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Day and hour summaries. Computed from MEASURES. Data from MEASURES are getting deleted some day; but SUMDATA are here for stay.';


DROP TABLE IF EXISTS `updates`;
CREATE TABLE `updates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `device_id` smallint(6) NOT NULL COMMENT 'ID zařízení',
  `fromVersion` varchar(200) COLLATE utf8_czech_ci NOT NULL COMMENT 'verze, ze které se aktualizuje',
  `fileHash` varchar(100) COLLATE utf8_czech_ci NOT NULL COMMENT 'hash souboru',
  `inserted` datetime NOT NULL COMMENT 'timestamp vložení',
  `downloaded` datetime DEFAULT NULL COMMENT 'timestamp stažení',
  PRIMARY KEY (`id`),
  KEY `device_id_fromVersion` (`device_id`,`fromVersion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `value_types`;
CREATE TABLE `value_types` (
  `id` int(11) NOT NULL,
  `unit` varchar(100) COLLATE utf8_czech_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Units for any kind of recorder values.';

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

DROP TABLE IF EXISTS `views`;
CREATE TABLE `views` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_czech_ci NOT NULL COMMENT 'Chart name - title in view window, name in left menu.',
  `vdesc` varchar(1024) COLLATE utf8_czech_ci NOT NULL COMMENT 'Description',
  `token` varchar(100) COLLATE utf8_czech_ci NOT NULL COMMENT 'Security token. All charts (views) with the same token will be displayed in together (with left menu for switching between)',
  `vorder` smallint(6) NOT NULL COMMENT 'Order - highest on top.',
  `render` varchar(10) COLLATE utf8_czech_ci NOT NULL DEFAULT 'view' COMMENT 'Which renderer to use ("view" is only available now)',
  `allow_compare` tinyint(4) NOT NULL DEFAULT 1 COMMENT 'Allow to select another year for compare?',
  `user_id` smallint(6) NOT NULL,
  `app_name` varchar(100) COLLATE utf8_czech_ci NOT NULL DEFAULT 'RatatoskrIoT' COMMENT 'Application name in top menu',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Chart views. Every VIEW (chart) has 0-N series defined in VIEW_DETAILS.';

INSERT INTO `views` (`id`, `name`, `vdesc`, `token`, `vorder`, `render`, `allow_compare`, `user_id`, `app_name`) VALUES
(1,	'Balkón',	'Balkón',	'hh5tjwxhs1t0n490pfxvurnd5oaksaxxl6rpadae',	1,	'chart',	0,	1,	'Balkón');

DROP TABLE IF EXISTS `view_detail`;
CREATE TABLE `view_detail` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `view_id` smallint(6) NOT NULL COMMENT 'Reference to VIEWS',
  `vorder` smallint(6) NOT NULL COMMENT 'Order in chart',
  `sensor_ids` varchar(30) COLLATE utf8_czech_ci NOT NULL COMMENT 'List of SENSORS, comma delimited',
  `y_axis` tinyint(4) NOT NULL COMMENT 'Which Y-axis to use? 1 or 2',
  `view_source_id` tinyint(4) NOT NULL COMMENT 'Which kind of data to load (references to VIEW_SOURCE)',
  `color_1` varchar(20) COLLATE utf8_czech_ci NOT NULL DEFAULT '255,0,0' COMMENT 'Color (R,G,B) for primary data',
  `color_2` varchar(20) COLLATE utf8_czech_ci NOT NULL DEFAULT '0,0,255' COMMENT 'Color (R,G,B) for comparison year',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='One serie for chart (VIEW).';

INSERT INTO `view_detail` (`id`, `view_id`, `vorder`, `sensor_ids`, `y_axis`, `view_source_id`, `color_1`, `color_2`) VALUES
(1,	1,	1,	'1',	1,	1,	'192,28,40',	'217,121,121'),
(2,	1,	2,	'2',	2,	1,	'97,53,131',	'192,97,203');

DROP TABLE IF EXISTS `view_source`;
CREATE TABLE `view_source` (
  `id` tinyint(4) NOT NULL,
  `desc` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `short_desc` varchar(255) COLLATE utf8_czech_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Types of views. Referenced from VIEW_DETAIL.';

INSERT INTO `view_source` (`id`, `desc`, `short_desc`) VALUES
(1,	'Automatická data',	'Automatická data'),
(2,	'Denní maximum',	'Denní maximum'),
(3,	'Denní minimum',	'Denní minimum'),
(4,	'Denní průměr',	'Denní průměr'),
(5,	'Vždy detailní data - na delších pohledech pomalé!',	'Detailní data'),
(6,	'Denní součet',	'Denní suma'),
(7,	'Hodinový součet',	'Hodinová suma'),
(8,	'Hodinové maximum',	'Hodinové maximum'),
(9,	'Hodinové/denní maximum',	'Do 90denních pohledů hodinové maximum, pro delší denní maximum'),
(10,	'Hodinový/denní součet',	'Pro krátké pohledy hodinový součet, pro dlouhé denní součet (typicky pro srážky)'),
(11,	'Týdenní součet',	'Týdenní součet (pro srážky)');

-- 2023-08-17 12:52:30
