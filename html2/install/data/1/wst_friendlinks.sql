/*
Navicat MySQL Data Transfer

Source Server         : 127.0.0.1
Source Server Version : 50617
Source Host           : localhost:3306
Source Database       : wstmall2

Target Server Type    : MYSQL
Target Server Version : 50617
File Encoding         : 65001

Date: 2016-10-17 15:20:43
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `wst_friendlinks`
-- ----------------------------
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

-- ----------------------------
-- Records of wst_friendlinks
-- ----------------------------
