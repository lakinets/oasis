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

 Date: 26/10/2026 18:10:29
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for ls
-- ----------------------------
DROP TABLE IF EXISTS `ls`;
CREATE TABLE `ls`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(54) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `ip` varchar(54) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `port` varchar(5) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `db_host` varchar(54) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `db_port` int UNSIGNED NOT NULL,
  `db_user` varchar(54) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `db_pass` varchar(54) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `db_name` varchar(54) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `telnet_host` varchar(54) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `telnet_port` int UNSIGNED NULL DEFAULT NULL,
  `telnet_pass` varchar(54) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `version` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `password_type` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `ix_id`(`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 3 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ls
-- ----------------------------

SET FOREIGN_KEY_CHECKS = 1;
