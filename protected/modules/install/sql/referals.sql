/*
Navicat MySQL Data Transfer

Source Server         : l2
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : l2

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2025-09-07 10:17:45
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `referals`
-- ----------------------------
DROP TABLE IF EXISTS `referals`;
CREATE TABLE `referals` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `referer` int(10) unsigned NOT NULL COMMENT 'ID кто пригласил',
  `referal` int(10) unsigned NOT NULL COMMENT 'ID кого пригласили',
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of referals
-- ----------------------------
