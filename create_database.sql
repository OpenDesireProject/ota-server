CREATE TABLE IF NOT EXISTS `updates` (
  `filename` varchar(255) NOT NULL,
  `device` varchar(16) NOT NULL,
  `incremental` char(10) NOT NULL,
  `timestamp` bigint(20) NOT NULL,
  `md5sum` char(32) NOT NULL,
  `channel` varchar(16) NOT NULL,
  `api_level` tinyint(4) NOT NULL,
  `url` varchar(255) NOT NULL,
  `changes` varchar(255) NOT NULL,
  `mirror_id` tinyint(4) NOT NULL,
  PRIMARY KEY (`url`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
