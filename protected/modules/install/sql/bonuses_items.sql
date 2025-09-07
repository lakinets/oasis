/*
Navicat MySQL Data Transfer

Source Server         : l2
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : l2

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2025-09-07 10:15:30
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `bonuses_items`
-- ----------------------------
DROP TABLE IF EXISTS `bonuses_items`;
CREATE TABLE `bonuses_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` int(10) unsigned NOT NULL COMMENT 'ID предмета',
  `count` int(10) unsigned NOT NULL DEFAULT 1 COMMENT 'Кол-во',
  `enchant` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'Заточка',
  `bonus_id` int(10) unsigned NOT NULL COMMENT 'ID бонуса к которому прицеплен предмет',
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of bonuses_items
-- ----------------------------
