/*
Navicat MySQL Data Transfer

Source Server         : l2
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : l2

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2025-09-07 10:16:54
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `migration`
-- ----------------------------
DROP TABLE IF EXISTS `migration`;
CREATE TABLE `migration` (
  `version` varchar(255) NOT NULL,
  `apply_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

-- ----------------------------
-- Records of migration
-- ----------------------------
INSERT INTO `migration` VALUES ('m000000_000000_base', '1427814707');
INSERT INTO `migration` VALUES ('m140725_121311_create_news_table', '1427814707');
INSERT INTO `migration` VALUES ('m140725_122011_create_bonus_codes_table', '1427814707');
INSERT INTO `migration` VALUES ('m140725_122402_create_bonus_codes_activated_logs_table', '1427814707');
INSERT INTO `migration` VALUES ('m140725_122556_create_bonuses_table', '1427814707');
INSERT INTO `migration` VALUES ('m140725_122757_create_bonuses_items_table', '1427814707');
INSERT INTO `migration` VALUES ('m140725_123052_create_config_table', '1427814707');
INSERT INTO `migration` VALUES ('m140725_123409_create_config_group_table', '1427814707');
INSERT INTO `migration` VALUES ('m140725_123539_create_gallery_table', '1427814707');
INSERT INTO `migration` VALUES ('m140725_123730_create_gs_table', '1427814707');
INSERT INTO `migration` VALUES ('m140725_124908_create_ls_table', '1427814707');
INSERT INTO `migration` VALUES ('m140725_125304_create_pages_table', '1427814707');
INSERT INTO `migration` VALUES ('m140725_125428_create_purchase_items_log_table', '1427814707');
INSERT INTO `migration` VALUES ('m140725_125815_create_referals_table', '1427814707');
INSERT INTO `migration` VALUES ('m140725_125938_create_referals_profit_table', '1427814707');
INSERT INTO `migration` VALUES ('m140725_130212_create_shop_categories_table', '1427814707');
INSERT INTO `migration` VALUES ('m140725_130712_create_shop_items_table', '1427814707');
INSERT INTO `migration` VALUES ('m140725_131457_create_shop_items_packs_table', '1427814707');
INSERT INTO `migration` VALUES ('m140725_131730_create_tickets_table', '1427814707');
INSERT INTO `migration` VALUES ('m140725_132039_create_tickets_answers_table', '1427814707');
INSERT INTO `migration` VALUES ('m140725_132210_create_tickets_categories_table', '1427814707');
INSERT INTO `migration` VALUES ('m140725_132408_create_transactions_table', '1427814707');
INSERT INTO `migration` VALUES ('m140725_132840_create_user_actions_log_table', '1427814707');
INSERT INTO `migration` VALUES ('m140725_133023_create_user_bonuses_table', '1427814707');
INSERT INTO `migration` VALUES ('m140725_133210_create_user_messages_table', '1427814707');
INSERT INTO `migration` VALUES ('m140725_133335_create_user_profiles_table', '1427814707');
INSERT INTO `migration` VALUES ('m140725_133554_create_users_table', '1427814707');
INSERT INTO `migration` VALUES ('m140725_134052_create_users_auth_logs_table', '1427814707');
INSERT INTO `migration` VALUES ('m140730_183829_seed_config_table', '1427814707');
INSERT INTO `migration` VALUES ('m140730_194400_seed_pages_table', '1427814707');
INSERT INTO `migration` VALUES ('m140803_105918_seed_tickets_categories', '1427814707');
INSERT INTO `migration` VALUES ('m140806_193126_fix', '1427814707');
INSERT INTO `migration` VALUES ('m140817_101237_fix', '1427814707');
INSERT INTO `migration` VALUES ('m140903_161418_fix', '1427814707');
