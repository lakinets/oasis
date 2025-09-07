/*
Navicat MySQL Data Transfer

Source Server         : l2
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : l2

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2025-09-07 10:20:04
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `user_profiles`
-- ----------------------------
DROP TABLE IF EXISTS `user_profiles`;
CREATE TABLE `user_profiles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `balance` decimal(10,2) unsigned NOT NULL DEFAULT 0.00 COMMENT 'Кол-во валюты',
  `vote_balance` decimal(10,2) unsigned NOT NULL DEFAULT 0.00 COMMENT 'Сколько раз проголосовал в рейтингах',
  `preferred_language` char(2) NOT NULL DEFAULT 'ru' COMMENT 'Предпочитаемый язык',
  `protected_ip` text DEFAULT NULL COMMENT 'IP адреса которые могут зайти в ЛК',
  `phone` varchar(54) DEFAULT NULL COMMENT 'Телефон юзера',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of user_profiles
-- ----------------------------
