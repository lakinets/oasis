/*
Navicat MySQL Data Transfer

Source Server         : l2
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : l2

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2025-09-07 10:17:26
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `payment_transactions`
-- ----------------------------
DROP TABLE IF EXISTS `payment_transactions`;
CREATE TABLE `payment_transactions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` varchar(64) NOT NULL,
  `provider` varchar(32) NOT NULL,
  `provider_invoice` varchar(128) DEFAULT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `login` varchar(64) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `currency` varchar(8) NOT NULL DEFAULT 'RUB',
  `status` enum('new','paid','fail') NOT NULL DEFAULT 'new',
  `payload` text DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `paid_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_id` (`order_id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_login` (`login`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of payment_transactions
-- ----------------------------
