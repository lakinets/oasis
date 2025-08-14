<?php

use yii\db\Migration;

class m250719_181343_init_ghtweb extends Migration
{
    public function safeUp()
    {
        // 1. Таблица пользователей
        $this->createTable('users', [
            'id'            => $this->primaryKey(),
            'username'      => $this->string(50)->notNull()->unique(),
            'password_hash' => $this->string(255)->notNull(),
            'email'         => $this->string(255)->notNull()->unique(),
            'auth_key'      => $this->string(32),
            'status'        => $this->smallInteger()->defaultValue(1),
            'created_at'    => $this->integer(),
            'updated_at'    => $this->integer(),
        ]);

        // 2. Таблица конфигурации
        $this->createTable('config', [
            'name'  => $this->string(255)->notNull()->unique(),
            'value' => $this->text(),
        ]);

        // 3. Таблица новостей
        $this->createTable('news', [
            'id'         => $this->primaryKey(),
            'title'      => $this->string(255)->notNull(),
            'content'    => $this->text(),
            'image'      => $this->string(255),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);

        // 4. Таблица магазина категорий
        $this->createTable('shop_categories', [
            'id'    => $this->primaryKey(),
            'name'  => $this->string(255)->notNull(),
            'order' => $this->integer()->defaultValue(0),
        ]);

        // 5. Таблица товаров магазина
        $this->createTable('shop_items', [
            'id'          => $this->primaryKey(),
            'category_id' => $this->integer()->notNull(),
            'name'        => $this->string(255)->notNull(),
            'description' => $this->text(),
            'price'       => $this->integer()->notNull(),
            'image'       => $this->string(255),
            'item_id'     => $this->integer()->notNull(),
            'amount'      => $this->integer()->defaultValue(1),
            'enchant'     => $this->integer()->defaultValue(0),
        ]);

        // 6. Таблица транзакций
        $this->createTable('transactions', [
            'id'         => $this->primaryKey(),
            'user_id'    => $this->integer()->notNull(),
            'amount'     => $this->decimal(10, 2)->notNull(),
            'type'       => $this->string(50)->notNull(),
            'status'     => $this->string(50)->defaultValue('pending'),
            'created_at' => $this->integer(),
        ]);

        // 7. Таблица тикетов
        $this->createTable('tickets', [
            'id'         => $this->primaryKey(),
            'user_id'    => $this->integer()->notNull(),
            'subject'    => $this->string(255)->notNull(),
            'message'    => $this->text()->notNull(),
            'status'     => $this->string(20)->defaultValue('open'),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);

        // 8. Таблица категорий тикетов
        $this->createTable('tickets_categories', [
            'id'   => $this->primaryKey(),
            'name' => $this->string(255)->notNull()->unique(),
        ]);

        // 9. Таблица бонус-кодов
        $this->createTable('bonus_codes', [
            'id'     => $this->primaryKey(),
            'code'   => $this->string(255)->notNull()->unique(),
            'reward' => $this->string(255)->notNull(),
            'uses'   => $this->integer()->defaultValue(1),
            'used'   => $this->integer()->defaultValue(0),
        ]);

        // Внешние ключи
        $this->addForeignKey('fk_shop_items_category', 'shop_items', 'category_id', 'shop_categories', 'id', 'CASCADE');
        $this->addForeignKey('fk_transactions_user', 'transactions', 'user_id', 'users', 'id', 'CASCADE');
        $this->addForeignKey('fk_tickets_user', 'tickets', 'user_id', 'users', 'id', 'CASCADE');
    }

    public function safeDown()
    {
        // Удаляем в обратном порядке
        $this->dropForeignKey('fk_tickets_user', 'tickets');
        $this->dropForeignKey('fk_transactions_user', 'transactions');
        $this->dropForeignKey('fk_shop_items_category', 'shop_items');

        $this->dropTable('bonus_codes');
        $this->dropTable('tickets_categories');
        $this->dropTable('tickets');
        $this->dropTable('transactions');
        $this->dropTable('shop_items');
        $this->dropTable('shop_categories');
        $this->dropTable('news');
        $this->dropTable('config');
        $this->dropTable('users');
    }
}