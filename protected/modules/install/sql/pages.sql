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

 Date: 15/10/2026 21:01:40
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for pages
-- ----------------------------
DROP TABLE IF EXISTS `pages`;
CREATE TABLE `pages`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `page` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `text` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `seo_title` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `seo_keywords` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `seo_description` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `ix_page`(`page`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 6 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of pages
-- ----------------------------
INSERT INTO `pages` VALUES (1, 'main', 'Добро пожаловать', 'Здесь будет отображена информация та которую будут видеть пользователи при посещении сайта.\r\nАдмин панель ваш-домен/backend', 'Добро пожаловать.', '', '', 1, '2015-03-31 18:11:47', '2026-10-15 20:52:46');
INSERT INTO `pages` VALUES (2, 'about', 'О сервере', 'Здесь вы можете рассказать о своем проекте.', '', '', '', 1, '2015-03-31 18:11:47', '2026-10-15 20:54:11');
INSERT INTO `pages` VALUES (3, 'files', 'Файлы', 'Здесь вы можете рассказать о том где брать файлы для игры на сервере.', '', '', '', 1, '2015-04-03 22:08:46', '2026-10-15 20:55:00');
INSERT INTO `pages` VALUES (4, 'onlypc', 'Только для ПК!', 'Уважаемый игрок, эта игра не доступна на мобильной платформе, игра доступна на Windows XP/7/8/10/11, возвращайся сюда с устройств ПК и окунусь с головой в мир приключений. Сейчас вам доступны действия только на сайте, если вы ранее уже играли на нашем сервере просто войдите в свой аккаунт и вы сможете совершать в своем личном кабинете все те же действия что и с вашего ПК.', '', '', '', 1, '2025-08-31 11:19:48', '2026-10-15 20:56:20');

SET FOREIGN_KEY_CHECKS = 1;
