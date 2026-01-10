/*
Navicat MySQL Data Transfer

Source Server         : l2
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : l2

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2025-09-07 10:16:02
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `config`
-- ----------------------------
DROP TABLE IF EXISTS `config`;
CREATE TABLE `config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `param` varchar(128) NOT NULL,
  `value` text NOT NULL,
  `default` text NOT NULL,
  `label` varchar(255) NOT NULL,
  `group_id` int(10) unsigned NOT NULL COMMENT 'ID группы',
  `order` tinyint(1) unsigned NOT NULL DEFAULT 1 COMMENT 'Сортировка',
  `method` varchar(54) DEFAULT NULL COMMENT 'Метод который будет вызван',
  `field_type` varchar(54) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `param` (`param`)
) ENGINE=MyISAM AUTO_INCREMENT=171 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of config
-- ----------------------------
INSERT INTO `config` VALUES ('1', 'theme', 'oasis', '', 'Тема сайта', '1', '1', 'getThemes', 'dropDownList', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('2', 'meta.title', '', 'GHTWEB CMS для Lineage2 серверов', 'Название сайта, используется в <title> и письмах', '1', '2', '', 'textField', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('3', 'meta.description', '', 'GHTWEB CMS для Lineage2 серверов', 'Описание сайта, используется в <meta name=\"description\">', '1', '3', '', 'textField', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('4', 'meta.keywords', '', 'GHTWEB CMS для Lineage2 серверов', 'Ключевые слова сайта, используется в <meta name=\"keywords\">', '1', '4', '', 'textField', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('5', 'meta.title_divider', ' - ', ' - ', 'Разделить в <title>', '1', '5', '', 'textField', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('6', 'index.type', 'news', 'page', 'Что выводить на главной странице сайта', '1', '6', 'getIndexPageTypes', 'dropDownList', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('7', 'index.page', 'main', 'main', 'Страница для главной страницы (если выбран вывод на главную \"Страница\")', '1', '7', 'getPages', 'dropDownList', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('8', 'index.rss.url', '', '', 'RSS новости: Ссылка на RSS с которого будут браться данные', '1', '8', '', 'textField', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('9', 'index.rss.date_format', 'Y-m-d H:i', 'Y-m-d H:i', 'RSS новости: Формат даты', '1', '9', '', 'textField', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('10', 'index.rss.cache', '15', '15', 'RSS новости: Через сколько минут обновлять кэш', '1', '10', '', 'textField', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('11', 'index.rss.limit', '5', '5', 'RSS новости: Записей на страницу', '1', '11', '', 'textField', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('12', 'register.captcha.allow', '0', '0', 'Включить капчу', '2', '1', '', 'dropDownList', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('13', 'register.allow', '1', '1', 'Разрешить регистрацию новых пользователей', '2', '2', '', 'dropDownList', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('14', 'register.confirm_email', '0', '1', 'Подтверждение регистрации по Email', '2', '3', '', 'dropDownList', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('15', 'register.confirm_email.time', '180', '180', 'Время жизни ключа для активации аккаунта (в минутах)', '2', '4', '', 'textField', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('16', 'news.per_page', '10', '10', 'Новостей на странице', '3', '1', '', 'textField', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('17', 'news.date_format', 'Y-m-d H:i', 'Y-m-d H:i', 'Формат даты когда была создана новость, инфа - http://php.net/manual/ru/function.date.php', '3', '2', '', 'textField', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('18', 'news.detail.socials', '1', '0', 'Включить виджет социальных иконок при просмотре новости, инфа - http://share42.com/', '3', '3', '', 'dropDownList', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('19', 'mail.smtp', '0', '0', 'Использовать SMTP', '4', '1', '', 'dropDownList', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('20', 'mail.smtp_login', '', '', 'SMTP: логин', '4', '2', '', 'textField', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('21', 'mail.smtp_password', '', '', 'SMTP: пароль', '4', '3', '', 'textField', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('22', 'mail.admin_email', '', 'no-reply@admin.ru', 'Email Администратора', '4', '4', '', 'textField', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('23', 'mail.smtp_host', '', '', 'SMTP: хост/ip', '4', '5', '', 'textField', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('24', 'mail.admin_name', 'Game Admin', 'Вася Пупкин', 'Имя Администратора (подставляется в поле \"От кого\")', '4', '6', '', 'textField', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('25', 'mail.smtp_port', '', '465', 'SMTP: порт', '4', '7', '', 'textField', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('26', 'forgotten_password.captcha.allow', '0', '1', 'Включить капчу', '5', '1', '', 'dropDownList', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('27', 'forgotten_password.cache_time', '60', '60', 'Время жизни ключа для восстановления пароля, в минутах', '5', '2', '', 'textField', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('28', 'captcha.min_length', '3', '3', 'Мин. кол-во символов', '6', '1', '', 'textField', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('29', 'captcha.max_length', '6', '6', 'Макс. кол-во символов', '6', '2', '', 'textField', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('30', 'captcha.width', '95', '95', 'Ширина капчи', '6', '3', '', 'textField', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('31', 'captcha.height', '32', '32', 'Высота капчи', '6', '4', '', 'textField', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('32', 'forum_threads.allow', '1', '0', 'Включить вывод тем с форума', '7', '1', '', 'dropDownList', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('33', 'forum_threads.cache', '15', '15', 'Время кэширования, в минутах. 0 - не кэшировать', '7', '2', '', 'textField', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('34', 'forum_threads.limit', '4', '10', 'Сколько тем выводить', '7', '3', '', 'textField', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('35', 'forum_threads.db_host', '', '127.0.0.1', 'DB host', '7', '4', '', 'textField', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('36', 'forum_threads.db_port', '', '3306', 'DB port', '7', '5', '', 'textField', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('37', 'forum_threads.db_user', '', 'root', 'DB user', '7', '6', '', 'textField', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('38', 'forum_threads.db_pass', '', '', 'DB pass', '7', '7', '', 'textField', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('39', 'forum_threads.db_name', '', '', 'DB name', '7', '8', '', 'textField', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('40', 'forum_threads.type', 'phpbb', '', 'Тип форума', '7', '9', 'getForumTypes', 'dropDownList', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('41', 'forum_threads.prefix', 'new_', '', 'Префикс у таблиц', '7', '10', '', 'textField', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('42', 'forum_threads.date_format', 'Y-m-d H:i', 'Y:m:d H:i', 'Формат даты', '7', '11', '', 'textField', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('43', 'forum_threads.link', '', 'http://forum.ghtweb.ru', 'Ссылка на форум', '7', '12', '', 'textField', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('44', 'forum_threads.id_deny', '', '14, 28, 13', 'ID форумов которые запрещены к выводу, через запятую', '7', '13', '', 'textField', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('45', 'robokassa.test', '1', '1', 'Робокасса в тестовом режиме', '8', '1', '', 'dropDownList', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('46', 'robokassa.login', '', '', 'Ваш логин', '8', '2', '', 'textField', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('47', 'robokassa.password', '', '', 'Пароль1', '8', '3', '', 'passwordField', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('48', 'robokassa.password2', '', '', 'Пароль2', '8', '4', '', 'passwordField', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('49', 'unitpay.secret_key', '', '', 'Секретный ключ', '9', '1', '', 'textField', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('50', 'unitpay.project_id', '', '', 'ID проекта', '9', '4', '', 'textField', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('51', 'unitpay.public_key', '', '', 'Публичный ключ', '9', '3', '', 'textField', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('97', 'nowpayments.ipn_secret', '', '', 'NOWPayments IPN Secret', '11', '2', null, 'passwordField', '2025-08-25 20:07:00', null);
INSERT INTO `config` VALUES ('98', 'nowpayments.currency', 'USD', 'USD', 'Валюта по умолчанию', '11', '3', null, 'textField', '2025-08-25 20:07:00', null);
INSERT INTO `config` VALUES ('96', 'nowpayments.api_key', '', '', 'NOWPayments API Key', '11', '1', null, 'textField', '2025-08-25 20:07:00', null);
INSERT INTO `config` VALUES ('58', 'server_status.allow', '1', '1', 'Включить показ статуса сервера(ов)', '19', '1', '', 'dropDownList', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('57', 'server_status.cache', '15', '15', 'Через сколько минут обновлять данные', '19', '2', '', 'textField', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('59', 'top.pk.allow', '1', '0', 'Включить виджет топ пк', '12', '1', '', 'dropDownList', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('60', 'top.pk.gs_id', '1', '0', 'Сервер с которого брать данные', '12', '2', 'getGs', 'dropDownList', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('61', 'top.pk.limit', '10', '10', 'Сколько игроков выводить', '12', '3', '', 'textField', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('62', 'top.pk.cache', '15', '15', 'Через сколько минут обновлять данные', '12', '4', '', 'textField', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('63', 'top.pvp.allow', '1', '0', 'Включить виджет топ пвп', '13', '1', '', 'dropDownList', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('64', 'top.pvp.gs_id', '1', '0', 'Сервер с которого брать данные', '13', '2', 'getGs', 'dropDownList', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('65', 'top.pvp.limit', '10', '10', 'Сколько игроков выводить', '13', '3', '', 'textField', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('66', 'top.pvp.cache', '15', '15', 'Через сколько минут обновлять данные', '13', '4', '', 'textField', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('67', 'login.captcha.allow', '0', '1', 'Включить капчу', '14', '0', '', 'dropDownList', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('68', 'login.count_failed_attempts_for_blocked', '5', '3', 'Через сколько неудачных попыток авторизоваться юзер будет заблокирован', '14', '2', '', 'textField', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('69', 'login.failed_attempts_blocked_time', '15', '30', 'На сколько минут блокировать юзера', '14', '3', '', 'textField', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('70', 'referral_program.allow', '1', '1', 'Вкл/Выкл программу пригласи друга (реферальная программа)', '15', '1', '', 'dropDownList', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('71', 'referral_program.percent', '5', '5', 'Сколько % получит игрок от пожертвований за то что привел друга', '15', '2', '', 'textField', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('72', 'cabinet.referals.limit', '20', '20', 'Кол-во записей с рефералами на страницу', '16', '1', '', 'textField', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('73', 'cabinet.transaction_history.limit', '20', '20', 'Кол-во записей на странице история пополнений', '16', '2', '', 'textField', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('74', 'cabinet.auth_logs_limit', '15', '15', 'Кол-во записей на странице история авторизаций', '16', '3', '', 'textField', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('75', 'cabinet.change_password.captcha.allow', '0', '0', 'Капча при смене пароля от аккаунта', '16', '4', '', 'dropDownList', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('76', 'cabinet.user_messages_limit', '10', '10', 'Кол-во сообщения на странице личные сообщения', '16', '5', '', 'textField', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('77', 'cabinet.tickets.limit', '20', '20', 'Кол-во записей на странице  поддержка', '16', '6', '', 'textField', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('78', 'cabinet.bonuses.limit', '10', '10', 'Кол-во записей на странице бонусы', '16', '7', '', 'textField', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('79', 'cabinet.tickets.answers.limit', '20', '20', 'Кол-во записей на странице ответы на тикеты', '16', '8', '', 'textField', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('80', 'shop.item.limit', '5', '5', 'Сколько наборов выводить на страницу в магазине', '16', '9', '', 'textField', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('81', 'gallery.limit', '20', '20', 'Кол-во картинок на страницу', '17', '1', '', 'textField', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('82', 'gallery.big.width', '800', '800', 'Ширина большой фотки', '17', '2', '', 'textField', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('83', 'gallery.big.height', '800', '800', 'Высота большой фотки', '17', '3', '', 'textField', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('84', 'gallery.small.width', '150', '150', 'Ширина превьюшки', '17', '4', '', 'textField', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('85', 'gallery.small.height', '150', '150', 'Высота превьюшки', '17', '5', '', 'textField', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('86', 'prefixes.allow', '0', '1', 'Вкл/Выкл префиксы', '18', '1', '', 'dropDownList', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('87', 'prefixes.length', '3', '3', 'Кол-во символов в префиксе', '18', '2', '', 'textField', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('88', 'prefixes.count_for_list', '6', '6', 'Кол-во префиксов в выпадающем списке для выбора юзером', '18', '3', '', 'textField', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('89', 'captcha.bg.color', '#2D1A13', '#FFFFFF', 'Цвет текста', '6', '5', '', 'textField', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('90', 'captcha.font.color', '#FFFFFF', '#000000', 'Задний фон капчи', '6', '5', '', 'textField', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('91', 'register.multiemail', '0', '0', 'Разрешить регистрировать на один Email много аккаунтов', '2', '5', '', 'dropDownList', '2015-03-31 18:11:47', '2015-04-05 21:52:22');
INSERT INTO `config` VALUES ('94', 'payop.secret_key', '', '', 'PayOp Secret Key', '10', '2', null, 'passwordField', '2025-08-25 20:06:49', null);
INSERT INTO `config` VALUES ('95', 'payop.currency', 'USD', 'RUB', 'Валюта по умолчанию', '10', '3', null, 'textField', '2025-08-25 20:06:49', null);
INSERT INTO `config` VALUES ('93', 'payop.public_key', '', '', 'PayOp Public Key', '10', '1', null, 'textField', '2025-08-25 20:06:49', null);
INSERT INTO `config` VALUES ('161', 'payment.robokassa.enabled', '0', '0', '1 Включена платежная система 0 Отключена Robokassa', '8', '7', null, 'dropDownList', '2025-08-25 21:57:20', null);
INSERT INTO `config` VALUES ('159', 'payment.payop.enabled', '0', '0', '1 Включена платежная система 0 Отключена PayOp', '10', '7', null, 'dropDownList', '2025-08-25 21:47:41', null);
INSERT INTO `config` VALUES ('160', 'payment.payop.project_id', '', '', 'PayOp Project ID', '10', '6', null, 'textField', '2025-08-25 21:47:41', null);
INSERT INTO `config` VALUES ('162', 'payment.unitpay.enabled', '0', '0', '1 Включена платежная система 0 Отключена Unitpay', '9', '7', null, 'dropDownList', '2025-08-25 21:57:20', null);
INSERT INTO `config` VALUES ('163', 'payment.nowpayments.enabled', '0', '0', ' Включена платежная система 0 Отключена nowpayments', '11', '7', null, 'dropDownList', '2025-08-25 23:26:23', null);
INSERT INTO `config` VALUES ('164', 'payment.volet.enabled', '0', '0', '1 Включена платежная система 0 Отключена Volet', '20', '3', null, 'dropDownList', '2025-08-26 12:14:00', null);
INSERT INTO `config` VALUES ('165', 'payment.volet.api_id', '', '', 'Volet API ID', '20', '2', null, 'textField', '2025-08-26 12:14:00', null);
INSERT INTO `config` VALUES ('166', 'payment.volet.api_key', '', '', 'Volet API Key', '20', '1', null, 'passwordField', '2025-08-26 12:14:00', null);
INSERT INTO `config` VALUES ('167', 'payment.interkassa.enabled', '0', '0', '1 Включена платежная система 0 Отключена Interkassa', '21', '4', null, 'dropDownList', '2025-08-26 13:12:23', null);
INSERT INTO `config` VALUES ('168', 'payment.interkassa.checkout_id', '', '', 'Interkassa Checkout ID', '21', '1', null, 'textField', '2025-08-26 13:12:23', null);
INSERT INTO `config` VALUES ('169', 'payment.interkassa.secret_key', '', '', 'Interkassa Secret Key', '21', '2', null, 'passwordField', '2025-08-26 13:12:23', null);
INSERT INTO `config` VALUES ('170', 'payment.interkassa.test_key', '', '', 'Interkassa Test Key (опц.)', '21', '3', null, 'passwordField', '2025-08-26 13:12:23', null);
