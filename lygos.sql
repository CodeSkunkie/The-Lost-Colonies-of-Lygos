-- Adminer 3.5.0 MySQL dump

SET NAMES utf8;
SET foreign_key_checks = 0;
SET time_zone = 'SYSTEM';
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `buildings`;
CREATE TABLE `buildings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `colony_id` int(10) unsigned NOT NULL,
  `level` smallint(6) NOT NULL,
  `type` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `buildings` (`id`, `colony_id`, `level`, `type`) VALUES
(1,	1,	1,	0),
(2,	1,	1,	1),
(3,	1,	1,	2),
(4,	1,	2,	3),
(5,	1,	1,	4),
(6,	2,	1,	0),
(7,	0,	1,	5);

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `colonies` (`id`, `player_id`, `x_coord`, `y_coord`, `last_resource_update`, `resource1_capacity`, `resource1_stock`, `resource1_production_rate`, `resource1_consumption_rate`, `resource2_capacity`, `resource2_stock`, `resource2_production_rate`, `resource2_consumption_rate`, `resource3_capacity`, `resource3_stock`, `resource3_production_rate`, `resource3_consumption_rate`, `resource4_capacity`, `resource4_stock`, `resource4_production_rate`, `resource4_consumption_rate`) VALUES
(1,	1,	0,	0,	1412012540,	999,	999,	3000,	0,	999,	999,	3000,	0,	999,	970,	3000,	0,	999,	999,	3000,	5),
(2,	2,	-39,	-28,	0,	100,	100,	20,	0,	1000,	1000,	50,	0,	1000,	1000,	25,	0,	100,	100,	5,	2),
(3,	3,	-39,	-27,	0,	100,	100,	20,	0,	1000,	1000,	50,	0,	1000,	1000,	25,	0,	100,	100,	5,	2);

DROP TABLE IF EXISTS `failed_logins`;
CREATE TABLE `failed_logins` (
  `ip` varchar(15) NOT NULL DEFAULT '',
  `username` varchar(16) NOT NULL DEFAULT '',
  `time` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'UNIX timestamp'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `fleet_ships`;
CREATE TABLE `fleet_ships` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fleet_id` int(10) unsigned NOT NULL,
  `type` int(11) NOT NULL,
  `count` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `fleets`;
CREATE TABLE `fleets` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `owner` int(10) unsigned NOT NULL,
  `current_x_coord` int(10) unsigned NOT NULL,
  `current_y_coord` int(10) unsigned NOT NULL,
  `home_x_coord` int(10) unsigned NOT NULL,
  `home_y_coord` int(10) unsigned NOT NULL,
  `speed` smallint(6) NOT NULL,
  `primary_objective` smallint(6) NOT NULL,
  `secondary_objective` smallint(6) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `messages` (`id`, `from_player`, `to_player`, `message`, `subject`, `viewed`, `time`) VALUES
(1,	1,	2,	'How\'s it going?',	'hi',	1,	1411510899),
(2,	2,	1,	'OMG DID YOU JUST DO WHAT I THINK YOU DID???',	'WHAT???',	1,	1411520899),
(3,	2,	1,	'That is funny.',	'haha',	1,	1411520900),
(4,	2,	1,	'Here is the text of the message.',	'Test Message',	1,	1411579300);

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
  KEY `player_id` (`player_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `player_tiles_cache` (`id`, `player_id`, `x_coord`, `y_coord`, `player_has_vision`, `cache`, `cache_time`) VALUES
(1,	0,	1,	0,	1,	'placeholder',	1411520899),
(2,	0,	1,	1,	0,	'placeholder',	1411520899);

DROP TABLE IF EXISTS `players`;
CREATE TABLE `players` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(45) NOT NULL,
  `email` varchar(45) NOT NULL,
  `password` varchar(42) NOT NULL,
  `group` varchar(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `players` (`id`, `username`, `email`, `password`, `group`) VALUES
(1,	'blair',	'blairdaly@gmail.com',	'*1C4372EC7799F152723E21AA0BF4C557CAE4DA73',	'admin'),
(2,	'notblair',	'beep@boop.bop',	'*8FD4846DBD1E87F6C35F664712865403A231FEBD',	'playe'),
(3,	'Allen',	'allencct@gmail.com',	'*B8C3EC973FAD3072659EEE62E506DA91DE31A830',	'playe');

DROP TABLE IF EXISTS `traveling_fleets`;
CREATE TABLE `traveling_fleets` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fleet_id` int(10) unsigned NOT NULL,
  `from_x_coord` int(10) unsigned NOT NULL,
  `from_y_coord` int(10) unsigned NOT NULL,
  `to_x_coord` int(10) unsigned NOT NULL,
  `to_y_coord` int(10) unsigned NOT NULL,
  `departure_time` int(10) unsigned NOT NULL,
  `arrival_time` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fleet_id` (`fleet_id`),
  CONSTRAINT `traveling_fleets_ibfk_1` FOREIGN KEY (`fleet_id`) REFERENCES `fleets` (`id`)
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `users` (`id`, `username`, `password`, `cookie_login_key`, `email`, `group`, `date_registered`, `last_login`) VALUES
(1,	'Blair',	'*1C4372EC7799F152723E21AA0BF4C557CAE4DA73',	'a%g[RZ)Mu7NO{D4r#]OmN7Hozmuh6pJW9lR7i',	'blairdaly@gmail.com',	'admin',	0,	1409684573),
(2,	'notblair',	'*8FD4846DBD1E87F6C35F664712865403A231FEBD',	'',	'beep@boop.bop',	'player',	1411578234,	0),
(3,	'Allen',	'*B8C3EC973FAD3072659EEE62E506DA91DE31A830',	'',	'allencct@gmail.com',	'player',	1411579116,	0);

DROP TABLE IF EXISTS `world_objects`;
CREATE TABLE `world_objects` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` smallint(6) NOT NULL,
  `x_coord` int(10) NOT NULL,
  `y_coord` int(10) NOT NULL,
  `owner` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- 2014-10-03 23:25:21
