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

 Date: 20/10/2026 13:05:41
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for shop_topup_tiers
-- ----------------------------
DROP TABLE IF EXISTS `shop_topup_tiers`;
CREATE TABLE `shop_topup_tiers`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `topup_id` int UNSIGNED NOT NULL,
  `min_qty` int UNSIGNED NOT NULL DEFAULT 0,
  `max_qty` int UNSIGNED NOT NULL DEFAULT 0,
  `bonus_qty` int UNSIGNED NOT NULL DEFAULT 0,
  `sort` int NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_topup_id`(`topup_id` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of shop_topup_tiers
-- ----------------------------

SET FOREIGN_KEY_CHECKS = 1;
