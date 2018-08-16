SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `wst_friendlinks`;
CREATE TABLE `wst_friendlinks` (
  `friendlinkId` int(11) NOT NULL AUTO_INCREMENT,
  `friendlinkIco` varchar(150) DEFAULT '',
  `friendlinkName` varchar(50) NOT NULL DEFAULT '',
  `friendlinkUrl` varchar(150) NOT NULL DEFAULT '',
  `friendlinkSort` int(11) NOT NULL DEFAULT '0',
  `dataFlag` tinyint(4) NOT NULL DEFAULT '1',
  `createTime` datetime NOT NULL,
  PRIMARY KEY (`friendlinkId`),
  KEY `dataFlag` (`dataFlag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
