/*
Navicat MySQL Data Transfer

Source Server         : l2
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : l2

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2025-09-07 10:16:13
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `config_group`
-- ----------------------------
DROP TABLE IF EXISTS `config_group`;
CREATE TABLE `config_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL COMMENT 'Название группы',
  `order` tinyint(1) unsigned NOT NULL DEFAULT 1 COMMENT 'Сортировка',
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of config_group
-- ----------------------------
INSERT INTO `config_group` VALUES ('1', 'Основные', '1', '1');
INSERT INTO `config_group` VALUES ('2', 'Регистрация', '2', '1');
INSERT INTO `config_group` VALUES ('3', 'Новости', '3', '1');
INSERT INTO `config_group` VALUES ('4', 'Email', '4', '1');
INSERT INTO `config_group` VALUES ('5', 'Восстановление пароля', '5', '1');
INSERT INTO `config_group` VALUES ('6', 'Капча', '6', '1');
INSERT INTO `config_group` VALUES ('7', 'Виджет: Темы с форума', '7', '1');
INSERT INTO `config_group` VALUES ('8', 'Платежная система: Robokassa', '8', '1');
INSERT INTO `config_group` VALUES ('9', 'Платежная система: Unitpay', '9', '1');
INSERT INTO `config_group` VALUES ('10', 'Платежная система: PayOp', '9', '1');
INSERT INTO `config_group` VALUES ('11', 'Платежная система: NOWPayments', '10', '1');
INSERT INTO `config_group` VALUES ('12', 'Виджет: Топ ПК', '11', '1');
INSERT INTO `config_group` VALUES ('13', 'Виджет: Топ ПВП', '12', '1');
INSERT INTO `config_group` VALUES ('14', 'Авторизация', '13', '1');
INSERT INTO `config_group` VALUES ('15', 'Реферальная программа', '14', '1');
INSERT INTO `config_group` VALUES ('16', 'Личный кабинет', '15', '1');
INSERT INTO `config_group` VALUES ('17', 'Галерея', '16', '1');
INSERT INTO `config_group` VALUES ('18', 'Префиксы', '17', '1');
INSERT INTO `config_group` VALUES ('19', 'Виджет: Статус сервера', '11', '1');
INSERT INTO `config_group` VALUES ('20', 'Платежная система: Volet', '10', '1');
INSERT INTO `config_group` VALUES ('21', 'Платежная система: Interkassa ', '10', '1');
