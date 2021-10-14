
CREATE TABLE IF NOT EXISTS `jobs_connector_engine` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `token` varchar(255) NOT NULL,
  `feed_ids` longtext COMMENT '(DC2Type:array)',
  `configuration` longtext COMMENT '(DC2Type:object)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_D292B849999517A` (`name`),
  UNIQUE KEY `UNIQ_D292B8495F37A13B` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `jobs_context_definition` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `host` varchar(255) NOT NULL,
  `locale` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `object_connector_context` (`host`,`locale`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `jobs_connector_context_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `connector` int(11) DEFAULT NULL,
  `context_definition` int(11) DEFAULT NULL,
  `object_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `object_connector_context` (`object_id`,`connector`,`context_definition`),
  KEY `IDX_E08389F8148C456E` (`connector`),
  KEY `IDX_E08389F8E5EBE02D` (`context_definition`),
  CONSTRAINT `FK_E08389F8148C456E` FOREIGN KEY (`connector`) REFERENCES `jobs_connector_engine` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_E08389F8E5EBE02D` FOREIGN KEY (`context_definition`) REFERENCES `jobs_context_definition` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `jobs_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `connector` int(11) DEFAULT NULL,
  `object_id` int(11) DEFAULT NULL,
  `type` varchar(255) NOT NULL,
  `message` longtext NOT NULL,
  `creation_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_41A9C57B148C456E` (`connector`),
  CONSTRAINT `FK_41A9C57B148C456E` FOREIGN KEY (`connector`) REFERENCES `jobs_connector_engine` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;