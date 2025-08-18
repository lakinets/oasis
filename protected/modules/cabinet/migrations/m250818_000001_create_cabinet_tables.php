<?php

use yii\db\Migration;

class m250818_000001_create_cabinet_tables extends Migration
{
    public function safeUp()
    {
        // Таблица bonuses
        $this->createTable('{{%bonuses}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(255)->notNull(),
            'date_end' => $this->datetime(),
            'status' => $this->tinyInteger()->defaultValue(1),
        ]);

        // Таблица bonuses_items
        $this->createTable('{{%bonuses_items}}', [
            'id' => $this->primaryKey(),
            'bonus_id' => $this->integer()->notNull(),
            'item_id' => $this->integer()->notNull(),
            'count' => $this->integer()->notNull()->defaultValue(1),
            'enchant' => $this->integer()->defaultValue(0),
            'status' => $this->tinyInteger()->defaultValue(1),
        ]);

        // Таблица bonus_codes
        $this->createTable('{{%bonus_codes}}', [
            'id' => $this->primaryKey(),
            'bonus_id' => $this->integer()->notNull(),
            'code' => $this->string(128)->notNull(),
            'limit' => $this->integer()->defaultValue(0),
            'status' => $this->tinyInteger()->defaultValue(1),
        ]);

        // Таблица bonus_codes_activated_logs
        $this->createTable('{{%bonus_codes_activated_logs}}', [
            'id' => $this->primaryKey(),
            'code_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'created_at' => $this->datetime()->notNull(),
        ]);

        // Таблица tickets_categories
        $this->createTable('{{%tickets_categories}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(255)->notNull(),
            'status' => $this->tinyInteger()->defaultValue(1),
            'sort' => $this->integer()->defaultValue(0),
        ]);

        // Таблица tickets
        $this->createTable('{{%tickets}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'category_id' => $this->integer()->notNull(),
            'priority' => $this->tinyInteger()->defaultValue(0),
            'title' => $this->string(255)->notNull(),
            'char_name' => $this->string(255),
            'date_incident' => $this->string(128),
            'status' => $this->tinyInteger()->defaultValue(1),
            'new_message_for_user' => $this->tinyInteger()->defaultValue(0),
            'new_message_for_admin' => $this->tinyInteger()->defaultValue(0),
            'gs_id' => $this->integer(),
            'created_at' => $this->datetime()->notNull(),
            'updated_at' => $this->datetime(),
        ]);

        // Таблица tickets_answers
        $this->createTable('{{%tickets_answers}}', [
            'id' => $this->primaryKey(),
            'ticket_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'text' => $this->text()->notNull(),
            'created_at' => $this->datetime()->notNull(),
        ]);

        // Таблица shop_categories
        $this->createTable('{{%shop_categories}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'link' => $this->string(255)->notNull(),
            'sort' => $this->integer()->defaultValue(0),
            'status' => $this->tinyInteger()->defaultValue(1),
            'gs_id' => $this->integer()->notNull(),
        ]);

        // Таблица shop_items_packs
        $this->createTable('{{%shop_items_packs}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(255)->notNull(),
            'description' => $this->text(),
            'category_id' => $this->integer()->notNull(),
            'img' => $this->string(255),
            'sort' => $this->integer()->defaultValue(0),
            'status' => $this->tinyInteger()->defaultValue(1),
        ]);

        // Таблица shop_items
        $this->createTable('{{%shop_items}}', [
            'id' => $this->primaryKey(),
            'pack_id' => $this->integer()->notNull(),
            'item_id' => $this->integer()->notNull(),
            'description' => $this->text(),
            'cost' => $this->decimal(10,2)->notNull(),
            'discount' => $this->decimal(5,2)->defaultValue(0),
            'count' => $this->integer()->defaultValue(1),
            'enchant' => $this->integer()->defaultValue(0),
            'status' => $this->tinyInteger()->defaultValue(1),
        ]);

        // Таблица user_bonuses
        $this->createTable('{{%user_bonuses}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'bonus_id' => $this->integer()->notNull(),
            'status' => $this->tinyInteger()->defaultValue(0),
            'created_at' => $this->datetime()->notNull(),
        ]);

        // Таблица user_profiles
        $this->createTable('{{%user_profiles}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'balance' => $this->integer()->defaultValue(0),
            'vote_balance' => $this->integer()->defaultValue(0),
            'preferred_language' => $this->string(5)->defaultValue('ru'),
            'protected_ip' => $this->text(),
            'phone' => $this->string(20),
        ]);

        // Таблица user_messages
        $this->createTable('{{%user_messages}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'title' => $this->string(255)->notNull(),
            'text' => $this->text()->notNull(),
            'read' => $this->tinyInteger()->defaultValue(0),
            'created_at' => $this->datetime()->notNull(),
        ]);

        // Таблица users_auth_logs
        $this->createTable('{{%users_auth_logs}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'ip' => $this->string(25)->notNull(),
            'user_agent' => $this->string(255),
            'status' => $this->tinyInteger()->defaultValue(1),
            'created_at' => $this->datetime()->notNull(),
        ]);

        // Внешние ключи
        $this->addForeignKey('fk_bonus_items_bonus', '{{%bonuses_items}}', 'bonus_id', '{{%bonuses}}', 'id', 'CASCADE');
        $this->addForeignKey('fk_bonus_codes_bonus', '{{%bonus_codes}}', 'bonus_id', '{{%bonuses}}', 'id', 'CASCADE');
        $this->addForeignKey('fk_bonus_logs_code', '{{%bonus_codes_activated_logs}}', 'code_id', '{{%bonus_codes}}', 'id', 'CASCADE');
        $this->addForeignKey('fk_tickets_category', '{{%tickets}}', 'category_id', '{{%tickets_categories}}', 'id', 'CASCADE');
        $this->addForeignKey('fk_tickets_answers_ticket', '{{%tickets_answers}}', 'ticket_id', '{{%tickets}}', 'id', 'CASCADE');
        $this->addForeignKey('fk_shop_packs_category', '{{%shop_items_packs}}', 'category_id', '{{%shop_categories}}', 'id', 'CASCADE');
        $this->addForeignKey('fk_shop_items_pack', '{{%shop_items}}', 'pack_id', '{{%shop_items_packs}}', 'id', 'CASCADE');
        $this->addForeignKey('fk_user_bonuses_user', '{{%user_bonuses}}', 'user_id', '{{%user}}', 'id', 'CASCADE');
        $this->addForeignKey('fk_user_profiles_user', '{{%user_profiles}}', 'user_id', '{{%user}}', 'id', 'CASCADE');
        $this->addForeignKey('fk_user_messages_user', '{{%user_messages}}', 'user_id', '{{%user}}', 'id', 'CASCADE');
        $this->addForeignKey('fk_auth_logs_user', '{{%users_auth_logs}}', 'user_id', '{{%user}}', 'id', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_bonus_items_bonus', '{{%bonuses_items}}');
        $this->dropForeignKey('fk_bonus_codes_bonus', '{{%bonus_codes}}');
        $this->dropForeignKey('fk_bonus_logs_code', '{{%bonus_codes_activated_logs}}');
        $this->dropForeignKey('fk_tickets_category', '{{%tickets}}');
        $this->dropForeignKey('fk_tickets_answers_ticket', '{{%tickets_answers}}');
        $this->dropForeignKey('fk_shop_packs_category', '{{%shop_items_packs}}');
        $this->dropForeignKey('fk_shop_items_pack', '{{%shop_items}}');
        $this->dropForeignKey('fk_user_bonuses_user', '{{%user_bonuses}}');
        $this->dropForeignKey('fk_user_profiles_user', '{{%user_profiles}}');
        $this->dropForeignKey('fk_user_messages_user', '{{%user_messages}}');
        $this->dropForeignKey('fk_auth_logs_user', '{{%users_auth_logs}}');

        $this->dropTable('{{%bonuses}}');
        $this->dropTable('{{%bonuses_items}}');
        $this->dropTable('{{%bonus_codes}}');
        $this->dropTable('{{%bonus_codes_activated_logs}}');
        $this->dropTable('{{%tickets_categories}}');
        $this->dropTable('{{%tickets}}');
        $this->dropTable('{{%tickets_answers}}');
        $this->dropTable('{{%shop_categories}}');
        $this->dropTable('{{%shop_items_packs}}');
        $this->dropTable('{{%shop_items}}');
        $this->dropTable('{{%user_bonuses}}');
        $this->dropTable('{{%user_profiles}}');
        $this->dropTable('{{%user_messages}}');
        $this->dropTable('{{%users_auth_logs}}');
    }
}