/*
Navicat MySQL Data Transfer

Source Server         : l2
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : l2

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2025-09-07 10:17:18
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `pages`
-- ----------------------------
DROP TABLE IF EXISTS `pages`;
CREATE TABLE `pages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `page` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `text` text NOT NULL,
  `seo_title` varchar(255) DEFAULT NULL,
  `seo_keywords` varchar(255) DEFAULT NULL,
  `seo_description` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ix_page` (`page`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of pages
-- ----------------------------
INSERT INTO `pages` VALUES ('1', 'main', 'Добро пожаловать', 'Здесь будет отображена информация та которую будут видеть пользователи при посещении сайта.', 'Добро пожаловать.', '', '', '1', '2015-03-31 18:11:47', '2025-09-06 10:53:51');
INSERT INTO `pages` VALUES ('2', 'about', 'О сервере', 'Здесь вы можете рассказать о своем проекте.', '', '', '', '1', '2015-03-31 18:11:47', '2025-09-06 10:52:19');
INSERT INTO `pages` VALUES ('3', 'downloads', 'Файлы', 'Здесь вы можете рассказать о том где брать файлы для игры на сервере.', '', '', '', '1', '2015-04-03 22:08:46', '2025-09-06 10:53:22');
INSERT INTO `pages` VALUES ('4', 'events', 'Акции', 'В данный момент акций нету.', '', '', '', '1', '2015-04-04 12:31:01', '2025-08-18 20:33:39');
INSERT INTO `pages` VALUES ('5', 'onlypc', 'Только для ПК!', 'Уважаемый игрок, эта игра не доступна на мобильной платформе, игра доступна на Windows XP/7/8/10/11, возвращайся сюда с устройств ПК и окунусь с головой в мир приключений. Сейчас вам доступны действия только на сайте, если вы ранее уже играли на нашем сервере просто войдите в свой аккаунт и вы сможете совершать в своем личном кабинете все те же действия что и с вашего ПК.', '', '', '', '1', '2025-08-31 11:19:48', null);
