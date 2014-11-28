-- Adminer 3.5.0 MySQL dump

SET NAMES utf8;
SET foreign_key_checks = 0;
SET time_zone = 'SYSTEM';
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `alerts`;
CREATE TABLE `alerts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'alert id',
  `player_id` int(10) unsigned NOT NULL COMMENT 'player id',
  `timestamp` int(10) unsigned NOT NULL COMMENT 'time of alert',
  `alert_type` mediumint(9) NOT NULL COMMENT 'type of alert (e.g. battle report, construction report, etc.)',
  `alert_contents` text NOT NULL COMMENT 'alert message',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `buildings`;
CREATE TABLE `buildings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `colony_id` int(10) unsigned NOT NULL,
  `level` smallint(6) NOT NULL,
  `type` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=latin1;

INSERT INTO `buildings` (`id`, `colony_id`, `level`, `type`) VALUES
(1,	1,	2,	0),
(2,	1,	1,	1),
(3,	1,	1,	2),
(4,	1,	2,	3),
(5,	1,	1,	4),
(6,	2,	1,	0),
(8,	4,	1,	0),
(9,	5,	2,	0),
(10,	5,	1,	2),
(11,	6,	1,	0),
(12,	6,	1,	2),
(13,	7,	1,	0),
(14,	4,	1,	1),
(15,	4,	1,	4),
(16,	8,	1,	0),
(17,	8,	1,	2),
(18,	1,	1,	5),
(19,	9,	2,	0),
(20,	10,	2,	0);

DROP TABLE IF EXISTS `colonies`;
CREATE TABLE `colonies` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `player_id` int(11) NOT NULL,
  `x_coord` int(11) NOT NULL,
  `y_coord` int(11) NOT NULL,
  `last_resource_update` int(11) unsigned NOT NULL,
  `resource1_capacity` int(11) NOT NULL,
  `resource1_stock` float NOT NULL,
  `resource1_production_rate` int(11) NOT NULL,
  `resource1_consumption_rate` int(11) NOT NULL,
  `resource2_capacity` int(11) NOT NULL,
  `resource2_stock` float NOT NULL,
  `resource2_production_rate` int(11) NOT NULL,
  `resource2_consumption_rate` int(11) NOT NULL,
  `resource3_capacity` int(11) NOT NULL,
  `resource3_stock` float NOT NULL,
  `resource3_production_rate` int(11) NOT NULL,
  `resource3_consumption_rate` int(11) NOT NULL,
  `resource4_capacity` int(11) NOT NULL,
  `resource4_stock` float NOT NULL,
  `resource4_production_rate` int(11) NOT NULL,
  `resource4_consumption_rate` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;

INSERT INTO `colonies` (`id`, `player_id`, `x_coord`, `y_coord`, `last_resource_update`, `resource1_capacity`, `resource1_stock`, `resource1_production_rate`, `resource1_consumption_rate`, `resource2_capacity`, `resource2_stock`, `resource2_production_rate`, `resource2_consumption_rate`, `resource3_capacity`, `resource3_stock`, `resource3_production_rate`, `resource3_consumption_rate`, `resource4_capacity`, `resource4_stock`, `resource4_production_rate`, `resource4_consumption_rate`) VALUES
(1,	1,	0,	0,	1413824032,	999,	981.5,	3000,	0,	999,	961.5,	3000,	0,	999,	901.5,	3000,	0,	999,	916.487,	3000,	5),
(2,	2,	0,	2,	0,	100,	100,	20,	0,	1000,	1000,	50,	0,	1000,	1000,	25,	0,	100,	100,	5,	2),
(3,	3,	-39,	-27,	0,	100,	100,	20,	0,	1000,	1000,	50,	0,	1000,	1000,	25,	0,	100,	100,	5,	2),
(4,	4,	22,	3,	1413213176,	100,	80.0667,	10,	2,	100,	60.0833,	13,	0,	100,	40.0833,	10,	0,	100,	20.0667,	13,	2),
(5,	5,	-10,	-38,	1412523264,	100,	70.3822,	13,	2,	100,	40.4778,	10,	0,	100,	5.47778,	10,	0,	100,	30.3822,	10,	2),
(6,	6,	18,	-47,	1412617695,	100,	75.0178,	13,	2,	100,	45.0222,	10,	0,	100,	50.0222,	10,	0,	100,	40.0178,	10,	2),
(7,	7,	40,	13,	0,	100,	100,	10,	2,	100,	100,	10,	0,	100,	100,	10,	0,	100,	100,	10,	2),
(8,	8,	16,	43,	1413302157,	100,	85,	13,	2,	100,	65,	10,	0,	100,	80,	10,	0,	100,	80,	10,	2),
(9,	9,	-11,	-46,	1413853128,	100,	85,	10,	2,	100,	75,	10,	0,	100,	25,	10,	0,	100,	50,	10,	2),
(10,	10,	34,	-2,	1413853744,	100,	85,	10,	2,	100,	75,	10,	0,	100,	25,	10,	0,	100,	50,	10,	2);

DROP TABLE IF EXISTS `failed_logins`;
CREATE TABLE `failed_logins` (
  `ip` varchar(15) NOT NULL DEFAULT '',
  `username` varchar(16) NOT NULL DEFAULT '',
  `time` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'UNIX timestamp'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `fleet_cargo`;
CREATE TABLE `fleet_cargo` (
  `fleet_id` int(10) unsigned NOT NULL,
  `food` int(10) unsigned NOT NULL,
  `water` int(10) unsigned NOT NULL,
  `metal` int(10) unsigned NOT NULL,
  `energy` int(10) unsigned NOT NULL,
  PRIMARY KEY (`fleet_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `fleet_ships`;
CREATE TABLE `fleet_ships` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fleet_id` int(10) unsigned NOT NULL,
  `type` int(11) NOT NULL,
  `count` int(11) NOT NULL,
  `special_orders` smallint(6) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `fleet_id_type` (`fleet_id`,`type`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

INSERT INTO `fleet_ships` (`id`, `fleet_id`, `type`, `count`, `special_orders`) VALUES
(1,	1,	1,	100,	0),
(2,	1,	0,	80,	0),
(3,	1,	2,	30,	0),
(4,	1,	3,	35,	0),
(5,	2,	2,	10,	0),
(6,	3,	0,	10,	0);

DROP TABLE IF EXISTS `fleets`;
CREATE TABLE `fleets` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `owner` int(10) unsigned NOT NULL COMMENT 'player_id',
  `current_x_coord` int(10) NOT NULL,
  `current_y_coord` int(10) NOT NULL,
  `home_x_coord` int(10) NOT NULL,
  `home_y_coord` int(10) NOT NULL,
  `from_x_coord` int(10) NOT NULL,
  `from_y_coord` int(10) NOT NULL,
  `to_x_coord` int(10) NOT NULL,
  `to_y_coord` int(10) NOT NULL,
  `speed` float NOT NULL COMMENT 'tiles per hour',
  `primary_objective` smallint(6) NOT NULL COMMENT 'see: Fleet.php',
  `secondary_objective` smallint(6) NOT NULL COMMENT 'see: Fleet.php',
  `traveling` smallint(6) NOT NULL COMMENT '0 or 1',
  `departure_time` int(10) unsigned NOT NULL COMMENT 'unix timestamp',
  `arrival_time` int(10) unsigned NOT NULL COMMENT 'unix timestamp',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

INSERT INTO `fleets` (`id`, `owner`, `current_x_coord`, `current_y_coord`, `home_x_coord`, `home_y_coord`, `from_x_coord`, `from_y_coord`, `to_x_coord`, `to_y_coord`, `speed`, `primary_objective`, `secondary_objective`, `traveling`, `departure_time`, `arrival_time`) VALUES
(1,	1,	0,	0,	0,	0,	0,	0,	0,	0,	20,	0,	0,	0,	0,	0),
(2,	2,	0,	2,	0,	2,	0,	0,	0,	0,	10,	1,	0,	0,	0,	0),
(3,	2,	2,	0,	0,	2,	0,	2,	2,	0,	10,	1,	0,	0,	0,	0);

DROP TABLE IF EXISTS `job_queue`;
CREATE TABLE `job_queue` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `colony_id` int(10) unsigned NOT NULL,
  `type` int(10) unsigned NOT NULL COMMENT 'Indicates if this job is for research, ship building, or building building. See classes/Job.php for exact usage.',
  `product_id` int(10) unsigned NOT NULL COMMENT 'The database row id of a building/ship/research-item',
  `product_type` mediumint(9) NOT NULL COMMENT 'The type of the ship/building/research-item',
  `start_time` int(10) unsigned NOT NULL,
  `duration` mediumint(9) NOT NULL,
  `completion_time` int(10) unsigned NOT NULL,
  `repeat_count` mediumint(9) NOT NULL COMMENT 'This job represents [repeat_count] more jobs identical to this one if repeat_count is > 0.',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;

INSERT INTO `job_queue` (`id`, `colony_id`, `type`, `product_id`, `product_type`, `start_time`, `duration`, `completion_time`, `repeat_count`) VALUES
(5,	6,	0,	0,	1,	1412617687,	70,	1412617757,	0);

DROP TABLE IF EXISTS `login_freeze`;
CREATE TABLE `login_freeze` (
  `ip` varchar(15) NOT NULL,
  `frozen_until` bigint(20) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `login_freeze` (`ip`, `frozen_until`) VALUES
('50.167.213.35',	1394910649);

DROP TABLE IF EXISTS `messages`;
CREATE TABLE `messages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `from_player` int(10) unsigned NOT NULL,
  `to_player` int(10) unsigned NOT NULL,
  `message` text NOT NULL,
  `subject` varchar(25) NOT NULL,
  `viewed` tinyint(4) NOT NULL,
  `time` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

INSERT INTO `messages` (`id`, `from_player`, `to_player`, `message`, `subject`, `viewed`, `time`) VALUES
(1,	1,	2,	'How\'s it going?',	'hi',	1,	1411510899),
(2,	2,	1,	'OMG DID YOU JUST DO WHAT I THINK YOU DID???',	'WHAT???',	1,	1411520899),
(3,	2,	1,	'That is funny.',	'haha',	1,	1411520900),
(4,	2,	1,	'Here is the text of the message.',	'Test Message',	1,	1411579300),
(5,	6,	2,	'LET US COMBINE OUR FLEETS SIR',	'i love u',	1,	1412617652),
(6,	1,	9,	'sup fegt',	'hi',	1,	1413853462),
(7,	9,	1,	'the game',	'your game',	0,	1413853599);

DROP TABLE IF EXISTS `player_tiles_cache`;
CREATE TABLE `player_tiles_cache` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `player_id` int(10) unsigned NOT NULL,
  `x_coord` int(10) NOT NULL,
  `y_coord` int(10) NOT NULL,
  `player_has_vision` tinyint(4) NOT NULL,
  `cache` text NOT NULL,
  `cache_time` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `player_id_x_coord_y_coord` (`player_id`,`x_coord`,`y_coord`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

INSERT INTO `player_tiles_cache` (`id`, `player_id`, `x_coord`, `y_coord`, `player_has_vision`, `cache`, `cache_time`) VALUES
(1,	1,	0,	0,	1,	'placeholder',	1411520899),
(2,	0,	1,	1,	0,	'placeholder',	1411520899);

DROP TABLE IF EXISTS `players`;
CREATE TABLE `players` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(45) NOT NULL,
  `email` varchar(45) NOT NULL,
  `password` varchar(42) NOT NULL,
  `group` varchar(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;

INSERT INTO `players` (`id`, `username`, `email`, `password`, `group`) VALUES
(1,	'blair',	'blairdaly@gmail.com',	'*1C4372EC7799F152723E21AA0BF4C557CAE4DA73',	'admin'),
(2,	'notblair',	'beep@boop.bop',	'*8FD4846DBD1E87F6C35F664712865403A231FEBD',	'playe'),
(3,	'Allen',	'allencct@gmail.com',	'*B8C3EC973FAD3072659EEE62E506DA91DE31A830',	'playe'),
(4,	'Kristydaly ',	'Kristydaly@gmail.com',	'*245FA3B2D380B83B45CAB18FD54A4F47B9308F5A',	'playe'),
(5,	'Rich Daly',	'rcdaly@yahoo.com',	'*A126095CEB37D568193829B2CEEC9E69E0B0A991',	'playe'),
(6,	'jbinder3',	'JRBinder@bellsouth.net',	'*F675CCC4C63470F04193EFE9D063F11BE47751B7',	'playe'),
(7,	'faggotmaster420blazeit#noscopeyoloswag',	'jbinder3@gmail.com',	'*F675CCC4C63470F04193EFE9D063F11BE47751B7',	'playe'),
(8,	'new',	'new@new.new',	'*DCB7DF5FFC82C441503300FFF165257BC551A598',	'playe'),
(9,	'retz',	'chris.valdivia9@yahoo.com',	'*6357AA0CCFD74F4CD677F102F55C306F38301A7F',	'playe'),
(10,	'Steph',	'steph.vance@outlook.com',	'*245FA3B2D380B83B45CAB18FD54A4F47B9308F5A',	'playe');

DROP TABLE IF EXISTS `research`;
CREATE TABLE `research` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `player_id` int(10) unsigned NOT NULL,
  `type` smallint(6) NOT NULL,
  `level` smallint(6) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(16) NOT NULL,
  `password` varchar(42) NOT NULL,
  `cookie_login_key` varchar(42) NOT NULL,
  `email` varchar(35) NOT NULL,
  `group` varchar(6) NOT NULL,
  `date_registered` bigint(20) unsigned NOT NULL,
  `last_login` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `username` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;

INSERT INTO `users` (`id`, `username`, `password`, `cookie_login_key`, `email`, `group`, `date_registered`, `last_login`) VALUES
(1,	'Blair',	'*1C4372EC7799F152723E21AA0BF4C557CAE4DA73',	'NoSD6!azEWzGUMc2Tb_N3!Ua!93W-CKJCZN4eM',	'blairdaly@gmail.com',	'admin',	0,	1417148963),
(2,	'notblair',	'*8FD4846DBD1E87F6C35F664712865403A231FEBD',	'',	'beep@boop.bop',	'player',	1411578234,	0),
(3,	'Allen',	'*B8C3EC973FAD3072659EEE62E506DA91DE31A830',	'',	'allencct@gmail.com',	'player',	1411579116,	0),
(4,	'Kristydaly ',	'*245FA3B2D380B83B45CAB18FD54A4F47B9308F5A',	'',	'Kristydaly@gmail.com',	'player',	1412518900,	0),
(5,	'Rich Daly',	'*A126095CEB37D568193829B2CEEC9E69E0B0A991',	'',	'rcdaly@yahoo.com',	'player',	1412522832,	0),
(6,	'jbinder3',	'*F675CCC4C63470F04193EFE9D063F11BE47751B7',	'',	'JRBinder@bellsouth.net',	'player',	1412617489,	0),
(7,	'faggotmaster420b',	'*F675CCC4C63470F04193EFE9D063F11BE47751B7',	'',	'jbinder3@gmail.com',	'player',	1412617838,	0),
(8,	'new',	'*DCB7DF5FFC82C441503300FFF165257BC551A598',	'',	'new@new.new',	'player',	1413301447,	0),
(9,	'retz',	'*6357AA0CCFD74F4CD677F102F55C306F38301A7F',	'',	'chris.valdivia9@yahoo.com',	'player',	1413853109,	0),
(10,	'Steph',	'*245FA3B2D380B83B45CAB18FD54A4F47B9308F5A',	'',	'steph.vance@outlook.com',	'player',	1413853690,	0);

DROP TABLE IF EXISTS `world_objects`;
CREATE TABLE `world_objects` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` smallint(6) NOT NULL,
  `x_coord` int(10) NOT NULL,
  `y_coord` int(10) NOT NULL,
  `owner` int(10) unsigned NOT NULL,
  `mass` int(10) NOT NULL,
  `building_type` smallint(5) unsigned NOT NULL COMMENT 'used only for ruins',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- 2014-11-28 04:39:41
