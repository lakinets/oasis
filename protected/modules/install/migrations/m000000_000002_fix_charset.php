<?php
use yii\db\Migration;

/**
 * Перекодировка уже испорченных русских текстов обратно в utf8mb4
 * Запускается ОДИН РАЗ командой:  php yii migrate --migrationPath=@app/modules/install/migrations
 */
class m000000_000002_fix_charset extends Migration
{
    public function safeUp()
    {
        // 1. Переводим таблицы в utf8mb4
        $tables = $this->db->createCommand("SHOW TABLES")->queryColumn();
        foreach ($tables as $table) {
            $this->execute("ALTER TABLE `{$table}` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        }

        // 2. Чиним строки, которые были вставлены в latin1 (двойное перекодирование)
        $this->execute("
            UPDATE pages
            SET title = CONVERT(CAST(CONVERT(title USING latin1) AS BINARY) USING utf8mb4),
                body  = CONVERT(CAST(CONVERT(body  USING latin1) AS BINARY) USING utf8mb4)
            WHERE LENGTH(title) != CHAR_LENGTH(title)
        ");
        /* Если есть другие таблицы с текстом – добавьте такие же 2 строки */
    }

    public function safeDown()
    {
        echo "Откат невозможен – данные уже исправлены.\n";
        return false;
    }
}