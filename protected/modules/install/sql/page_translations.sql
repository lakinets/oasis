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

 Date: 15/10/2026 21:01:30
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for page_translations
-- ----------------------------
DROP TABLE IF EXISTS `page_translations`;
CREATE TABLE `page_translations`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `page_id` int UNSIGNED NOT NULL,
  `language` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `seo_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `seo_keywords` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `seo_description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `idx-pt-page-lang`(`page_id` ASC, `language` ASC) USING BTREE,
  INDEX `idx-pt-lang`(`language` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 106 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of page_translations
-- ----------------------------
INSERT INTO `page_translations` VALUES (1, 1, 'ru', 'Добро пожаловать', 'Здесь будет отображена информация та которую будут видеть пользователи при посещении сайта.\r\nАдмин панель ваш-домен/backend', 'Добро пожаловать.', '', '');
INSERT INTO `page_translations` VALUES (2, 2, 'ru', 'О сервере', 'Здесь вы можете рассказать о своем проекте.', '', '', '');
INSERT INTO `page_translations` VALUES (3, 3, 'ru', 'Файлы', 'Здесь вы можете рассказать о том где брать файлы для игры на сервере.', '', '', '');
INSERT INTO `page_translations` VALUES (4, 4, 'ru', 'Только для ПК!', 'Уважаемый игрок, эта игра не доступна на мобильной платформе, игра доступна на Windows XP/7/8/10/11, возвращайся сюда с устройств ПК и окунусь с головой в мир приключений. Сейчас вам доступны действия только на сайте, если вы ранее уже играли на нашем сервере просто войдите в свой аккаунт и вы сможете совершать в своем личном кабинете все те же действия что и с вашего ПК.', '', '', '');
INSERT INTO `page_translations` VALUES (52, 1, 'en', 'Welcome', 'This is where the information that users will see when visiting the site will be displayed.\r\nAdmin panel: your-domain/backend', '', '', '');
INSERT INTO `page_translations` VALUES (103, 2, 'en', 'About the server', 'Here you can tell us about your project.', '', '', '');
INSERT INTO `page_translations` VALUES (104, 3, 'en', 'Files', 'Here you can explain where to get the files needed to play on the server.', '', '', '');
INSERT INTO `page_translations` VALUES (105, 4, 'en', 'For PC only!', 'Dear player, this game is not available on mobile platforms; it is supported on Windows XP/7/8/10/11. Please return using a PC to dive headfirst into a world of adventure. For now, you can only perform actions via the website; if you have played on our server before, simply log in to your account to access the same features in your personal dashboard that you would have from your PC.', '', '', '');

SET FOREIGN_KEY_CHECKS = 1;
