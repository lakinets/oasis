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

 Date: 20/10/2026 13:10:51
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for gs
-- ----------------------------
DROP TABLE IF EXISTS `gs`;
CREATE TABLE `gs`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(54) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `ip` varchar(54) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `port` int UNSIGNED NOT NULL,
  `db_host` varchar(54) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `db_port` int UNSIGNED NOT NULL,
  `db_user` varchar(54) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `db_pass` varchar(54) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `db_name` varchar(54) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `telnet_host` varchar(54) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `telnet_port` int UNSIGNED NULL DEFAULT NULL,
  `telnet_pass` varchar(54) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `login_id` tinyint UNSIGNED NOT NULL,
  `version` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `fake_online` int UNSIGNED NOT NULL DEFAULT 0,
  `allow_teleport` tinyint UNSIGNED NOT NULL DEFAULT 1,
  `teleport_time` int UNSIGNED NOT NULL DEFAULT 20,
  `stats_allow` tinyint UNSIGNED NOT NULL DEFAULT 1,
  `stats_cache_time` int UNSIGNED NOT NULL DEFAULT 10,
  `stats_total` tinyint UNSIGNED NOT NULL DEFAULT 1,
  `stats_pvp` tinyint UNSIGNED NOT NULL DEFAULT 1,
  `stats_pk` tinyint UNSIGNED NOT NULL DEFAULT 1,
  `stats_clans` tinyint UNSIGNED NOT NULL DEFAULT 1,
  `stats_castles` tinyint UNSIGNED NOT NULL DEFAULT 1,
  `stats_online` tinyint UNSIGNED NOT NULL DEFAULT 1,
  `stats_clan_info` tinyint UNSIGNED NOT NULL DEFAULT 1,
  `stats_top` tinyint UNSIGNED NOT NULL DEFAULT 1,
  `stats_rich` tinyint UNSIGNED NOT NULL DEFAULT 1,
  `stats_count_results` int UNSIGNED NOT NULL DEFAULT 20,
  `exp` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '1',
  `sp` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '1',
  `adena` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '1',
  `drop` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '1',
  `items` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '1',
  `spoil` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '1',
  `q_drop` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '1',
  `q_reward` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '1',
  `rb` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '1',
  `erb` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '1',
  `services_premium_allow` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Покупка премиума',
  `services_premium_cost` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL COMMENT 'Стоймость премиум аккаунта',
  `services_remove_hwid_allow` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Удаление привязки по HWID',
  `services_change_char_name_allow` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Смена имени персонажу',
  `services_change_char_name_cost` float UNSIGNED NOT NULL DEFAULT 300 COMMENT 'Стоймость смены ника персонажу',
  `services_change_char_name_chars` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '' COMMENT 'Символы которые можно ввести для нового ника',
  `services_change_gender_allow` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Смена пола персонажу',
  `services_change_gender_cost` float UNSIGNED NOT NULL DEFAULT 300 COMMENT 'Стоймость смены пола',
  `currency_name` varchar(128) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL COMMENT 'Название валюты сервера',
  `deposit_allow` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Вкл/Выкл возможность пополняться',
  `deposit_payment_system` tinyint UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Агрегатор',
  `deposit_desc` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL,
  `deposit_course_payments` decimal(10, 0) UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Курс валюты к игровой валюте',
  `currency_symbol` varchar(54) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT 'RUR' COMMENT 'Валюта, RUR, EUR, USD и т.д',
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `shop_mode` enum('shop','topup') CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT 'shop',
  `created_at` datetime NULL DEFAULT NULL,
  `updated_at` datetime NULL DEFAULT NULL,
  `stats_items` tinyint UNSIGNED NOT NULL DEFAULT 1,
  `stats_items_list` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `ixId`(`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 3 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of gs
-- ----------------------------
INSERT INTO `gs` VALUES (2, 'Test server', '127.0.0.1', 2106, '127.0.0.1', 3306, 'root', 'root', 'l2', NULL, NULL, NULL, 2, 'Pwsoft_it', 0, 1, 20, 1, 10, 1, 1, 1, 1, 1, 1, 1, 1, 1, 20, '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', 1, NULL, 1, 1, 300, '', 1, 300, NULL, 1, 0, NULL, 1, 'RUR', 1, 'topup', NULL, NULL, 1, NULL);

SET FOREIGN_KEY_CHECKS = 1;
