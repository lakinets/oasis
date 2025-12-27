/*
Navicat MySQL Data Transfer

Source Server         : Test
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : l2

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2025-12-27 22:19:10
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `shop_items_packs`
-- ----------------------------
DROP TABLE IF EXISTS `shop_items_packs`;
CREATE TABLE `shop_items_packs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL COMMENT 'Название набора',
  `description` text DEFAULT NULL,
  `category_id` int(10) unsigned NOT NULL,
  `img` varchar(128) DEFAULT NULL,
  `sort` int(10) unsigned NOT NULL DEFAULT 1,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of shop_items_packs
-- ----------------------------
