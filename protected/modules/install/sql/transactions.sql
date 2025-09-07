/*
Navicat MySQL Data Transfer

Source Server         : l2
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : l2

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2025-09-07 10:19:04
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `transactions`
-- ----------------------------
DROP TABLE IF EXISTS `transactions`;
CREATE TABLE `transactions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `payment_system` varchar(15) NOT NULL COMMENT 'Платёжная система (robokassa, waytopay и т.д)',
  `user_id` varchar(54) NOT NULL,
  `sum` decimal(10,2) unsigned NOT NULL COMMENT 'Сумма',
  `count` int(10) unsigned NOT NULL COMMENT 'Кол-во игровой валюты',
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `user_ip` varchar(54) DEFAULT NULL,
  `params` text DEFAULT NULL COMMENT 'Параметры которые прилетели',
  `gs_id` int(10) unsigned NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of transactions
-- ----------------------------
