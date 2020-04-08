CREATE TABLE `jobs_connector` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `configuration` longtext COMMENT '(DC2Type:object)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_BFE1697E999517A` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;