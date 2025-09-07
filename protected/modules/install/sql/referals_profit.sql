/*
Navicat MySQL Data Transfer

Source Server         : l2
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : l2

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2025-09-07 10:17:55
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `referals_profit`
-- ----------------------------
DROP TABLE IF EXISTS `referals_profit`;
CREATE TABLE `referals_profit` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `referer_id` int(10) unsigned NOT NULL COMMENT 'ID юзера который привёл',
  `referal_id` int(10) unsigned NOT NULL COMMENT 'ID юзера кто совершил сделку',
  `profit` float unsigned NOT NULL COMMENT 'Прибыль, % от суммы пополнения',
  `sum` float unsigned NOT NULL COMMENT 'На какую сумму совершен платеж',
  `percent` float unsigned NOT NULL COMMENT '% который был на момент совершения платежа',
  `transaction_id` int(10) unsigned NOT NULL COMMENT 'ID транзакции по которой было зачисление',
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of referals_profit
-- ----------------------------
