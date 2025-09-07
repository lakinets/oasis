/*
Navicat MySQL Data Transfer

Source Server         : l2
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : l2

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2025-09-07 10:18:40
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `tickets`
-- ----------------------------
DROP TABLE IF EXISTS `tickets`;
CREATE TABLE `tickets` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL COMMENT 'ID того кто создал',
  `category_id` smallint(5) unsigned NOT NULL COMMENT 'ID категории',
  `priority` tinyint(3) unsigned NOT NULL DEFAULT 1 COMMENT 'Приоритет',
  `date_incident` varchar(128) NOT NULL COMMENT 'Дата происшествия',
  `char_name` varchar(255) DEFAULT NULL COMMENT 'Имя персонажа',
  `title` varchar(255) NOT NULL COMMENT 'Тема',
  `status` tinyint(1) unsigned NOT NULL,
  `new_message_for_user` tinyint(1) NOT NULL DEFAULT 0,
  `new_message_for_admin` tinyint(1) NOT NULL DEFAULT 0,
  `gs_id` int(10) unsigned NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tickets
-- ----------------------------
