/*
Navicat MySQL Data Transfer

Source Server         : l2
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : l2

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2025-09-07 10:16:44
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `ls`
-- ----------------------------
DROP TABLE IF EXISTS `ls`;
CREATE TABLE `ls` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(54) NOT NULL,
  `ip` varchar(54) NOT NULL,
  `port` varchar(5) NOT NULL,
  `db_host` varchar(54) NOT NULL,
  `db_port` int(10) unsigned NOT NULL,
  `db_user` varchar(54) NOT NULL,
  `db_pass` varchar(54) DEFAULT NULL,
  `db_name` varchar(54) NOT NULL,
  `telnet_host` varchar(54) DEFAULT NULL,
  `telnet_port` int(10) unsigned DEFAULT NULL,
  `telnet_pass` varchar(54) DEFAULT NULL,
  `version` varchar(20) NOT NULL,
  `password_type` varchar(15) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ix_id` (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ls
-- ----------------------------
