/*
Navicat MySQL Data Transfer

Source Server         : Test
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : l2

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2026-01-10 20:35:17
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `services`
-- ----------------------------
DROP TABLE IF EXISTS `services`;
CREATE TABLE `services` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT 'РРјСЏ СЃРµСЂРІРёСЃР°',
  `cost` decimal(10,2) unsigned NOT NULL DEFAULT 0.00 COMMENT 'РЎС‚РѕРёРјРѕСЃС‚СЊ СЃРµСЂРІРёСЃР°',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1-РІРєР»СЋС‡РµРЅ, 0-РІС‹РєР»СЋС‡РµРЅ',
  `type` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of services
-- ----------------------------
INSERT INTO `services` VALUES ('1', 'Смена имени персонажа', '120.00', '1', 'change_name');
INSERT INTO `services` VALUES ('2', 'Смена пола персонажа', '150.00', '1', 'CHANGE_GENDER');
INSERT INTO `services` VALUES ('3', 'Снятие кармы', '50.00', '1', 'remove_karma');
INSERT INTO `services` VALUES ('4', 'Статус дворянина', '300.00', '1', 'NOBLE_STATUS');
INSERT INTO `services` VALUES ('5', 'Подарочный код', '10.00', '1', 'gift_code');
