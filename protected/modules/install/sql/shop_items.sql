/*
 Navicat Premium Dump SQL

 Source Server         : Test
 Source Server Type    : MySQL
 Source Server Version : 260700 (26.7.0)
 Source Host           : localhost:3306
 Source Schema         : test

 Target Server Type    : MySQL
 Target Server Version : 260700 (26.7.0)
 File Encoding         : 65001

 Date: 15/10/2026 22:55:11
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for shop_items
-- ----------------------------
DROP TABLE IF EXISTS `shop_items`;
CREATE TABLE `shop_items`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `pack_id` int UNSIGNED NOT NULL COMMENT 'ID набора',
  `item_id` int UNSIGNED NOT NULL COMMENT 'ID предмета',
  `description` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL,
  `description_en` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL,
  `cost` decimal(10, 2) UNSIGNED NOT NULL COMMENT 'Стоймость',
  `discount` float UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Скидка на товар',
  `currency_type` varchar(54) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT 'donat' COMMENT 'За какой тип валюты отдать предмет, vote - за голоса с рейтингов, donat - за рил бабки',
  `count` int UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Кол-во',
  `enchant` int NULL DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `sort` int UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Сортировка',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 102 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of shop_items
-- ----------------------------
INSERT INTO `shop_items` VALUES (51, 6, 57, 'Адена для теста', 'Adena for testing', 10.00, 15, 'donat', 100000, 0, 1, 1, '2025-12-27 23:23:35', '2026-10-15 22:45:57');

SET FOREIGN_KEY_CHECKS = 1;
