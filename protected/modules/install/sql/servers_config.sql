/*
Navicat MySQL Data Transfer

Source Server         : l2
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : l2

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2025-09-07 10:18:03
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `servers_config`
-- ----------------------------
DROP TABLE IF EXISTS `servers_config`;
CREATE TABLE `servers_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `db_host` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `db_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `db_user` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `db_pass` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `chronicle` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rates` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of servers_config
-- ----------------------------
