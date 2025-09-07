/*
Navicat MySQL Data Transfer

Source Server         : l2
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : l2

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2025-09-07 10:17:36
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `purchase_items_log`
-- ----------------------------
DROP TABLE IF EXISTS `purchase_items_log`;
CREATE TABLE `purchase_items_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pack_id` int(10) unsigned NOT NULL COMMENT 'ID набора',
  `item_id` int(10) unsigned NOT NULL COMMENT 'ID предмета',
  `description` text DEFAULT NULL,
  `cost` decimal(10,2) unsigned NOT NULL COMMENT 'Стоймость',
  `discount` float unsigned NOT NULL DEFAULT 0 COMMENT 'Скидка на товар',
  `currency_type` varchar(54) NOT NULL DEFAULT 'donat',
  `count` int(10) unsigned NOT NULL DEFAULT 1 COMMENT 'Кол-во',
  `enchant` smallint(5) unsigned NOT NULL COMMENT 'Заточка',
  `user_id` int(10) unsigned NOT NULL COMMENT 'ID того кто купил',
  `char_id` int(10) unsigned NOT NULL COMMENT 'ID персонажа которому упала шмотка',
  `gs_id` int(10) unsigned NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of purchase_items_log
-- ----------------------------
