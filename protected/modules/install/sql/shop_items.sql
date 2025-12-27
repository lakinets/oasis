/*
Navicat MySQL Data Transfer

Source Server         : Test
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : l2

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2025-12-27 22:22:40
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `shop_items`
-- ----------------------------
DROP TABLE IF EXISTS `shop_items`;
CREATE TABLE `shop_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pack_id` int(10) unsigned NOT NULL COMMENT 'ID набора',
  `item_id` int(10) unsigned NOT NULL COMMENT 'ID предмета',
  `description` text DEFAULT NULL,
  `cost` decimal(10,2) unsigned NOT NULL COMMENT 'Стоймость',
  `discount` float unsigned NOT NULL DEFAULT 0 COMMENT 'Скидка на товар',
  `currency_type` varchar(54) NOT NULL DEFAULT 'donat' COMMENT 'За какой тип валюты отдать предмет, vote - за голоса с рейтингов, donat - за рил бабки',
  `count` int(10) unsigned NOT NULL DEFAULT 1 COMMENT 'Кол-во',
  `enchant` int(11) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `sort` int(10) unsigned NOT NULL DEFAULT 1 COMMENT 'Сортировка',
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of shop_items
-- ----------------------------
