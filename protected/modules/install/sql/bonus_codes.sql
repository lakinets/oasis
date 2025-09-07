/*
Navicat MySQL Data Transfer

Source Server         : l2
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : l2

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2025-09-07 10:15:43
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `bonus_codes`
-- ----------------------------
DROP TABLE IF EXISTS `bonus_codes`;
CREATE TABLE `bonus_codes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bonus_id` int(10) unsigned NOT NULL,
  `code` varchar(128) NOT NULL COMMENT 'Бонус код',
  `limit` int(10) unsigned NOT NULL DEFAULT 1 COMMENT 'Кол-во активироваций',
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of bonus_codes
-- ----------------------------
