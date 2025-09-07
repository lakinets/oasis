/*
Navicat MySQL Data Transfer

Source Server         : l2
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : l2

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2025-09-07 10:19:30
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `user_actions_log`
-- ----------------------------
DROP TABLE IF EXISTS `user_actions_log`;
CREATE TABLE `user_actions_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `action_id` smallint(5) unsigned NOT NULL COMMENT 'ID того что сделал юзер',
  `params` text DEFAULT NULL COMMENT 'Параметры совершаемого действия',
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IxUserId` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of user_actions_log
-- ----------------------------
