/*
Navicat MySQL Data Transfer

Source Server         : l2
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : l2

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2025-09-07 10:19:55
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `user_messages`
-- ----------------------------
DROP TABLE IF EXISTS `user_messages`;
CREATE TABLE `user_messages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `message` text NOT NULL,
  `read` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of user_messages
-- ----------------------------
