/*
Navicat MySQL Data Transfer

Source Server         : l2
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : l2

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2025-09-07 10:18:57
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `tickets_categories`
-- ----------------------------
DROP TABLE IF EXISTS `tickets_categories`;
CREATE TABLE `tickets_categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL COMMENT 'Название',
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `sort` smallint(5) unsigned NOT NULL DEFAULT 1 COMMENT 'Сортировка',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tickets_categories
-- ----------------------------
INSERT INTO `tickets_categories` VALUES ('1', 'Учетная запись (аккаунт)', '1', '1');
INSERT INTO `tickets_categories` VALUES ('2', 'Нарушение правил', '1', '2');
INSERT INTO `tickets_categories` VALUES ('3', 'Пожертвования (донат)', '1', '3');
INSERT INTO `tickets_categories` VALUES ('4', 'Связь (вход в игру; задержки)', '1', '4');
INSERT INTO `tickets_categories` VALUES ('5', 'Технические проблемы', '1', '5');
INSERT INTO `tickets_categories` VALUES ('6', 'Ваши предложения, пожелания', '1', '6');
INSERT INTO `tickets_categories` VALUES ('7', 'Другие проблемы', '1', '7');
INSERT INTO `tickets_categories` VALUES ('8', 'Услуги', '1', '8');
