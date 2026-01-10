/*
Navicat MySQL Data Transfer

Source Server         : Test
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : l2

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2026-01-10 20:34:17
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `gift_codes`
-- ----------------------------
DROP TABLE IF EXISTS `gift_codes`;
CREATE TABLE `gift_codes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT 'СЃРѕР·РґР°С‚РµР»СЊ',
  `code` varchar(32) NOT NULL COMMENT 'СѓРЅРёРєР°Р»СЊРЅС‹Р№ РєРѕРґ',
  `nominal` int(11) NOT NULL COMMENT 'РЅРѕРјРёРЅР°Р» РІ Web-Aden',
  `cost` decimal(10,2) NOT NULL COMMENT 'СЃС‚РѕРёРјРѕСЃС‚СЊ СЃРѕР·РґР°РЅРёСЏ',
  `status` enum('active','activated') NOT NULL DEFAULT 'active',
  `created_at` datetime DEFAULT current_timestamp(),
  `activated_at` datetime DEFAULT NULL,
  `activated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_code` (`code`),
  KEY `idx_user` (`user_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of gift_codes
-- ----------------------------
