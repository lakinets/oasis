<?php
use yii\db\Migration;

/**
 * Выполняет структурные .sql-файлы в строгом порядке,
 * all_items.sql — ПОСЛЕДНИМ (можно исключить)
 */
class m000000_000001_run_all_sql_files extends Migration
{
    /* строгий порядок: сначала структура, потом данные */
    private array $order = [
        'users.sql',
        'users_auth_logs.sql',
        'user_actions_log.sql',
        'user_bonuses.sql',
        'user_messages.sql',
        'user_profiles.sql',
        'config_group.sql',
        'config.sql',
        'gs.sql',
        'ls.sql',
        'servers_config.sql',
        'news.sql',
        'pages.sql',
        'gallery.sql',
        'services.sql',
        'tickets_categories.sql',
        'tickets.sql',
        'tickets_answers.sql',
        'transactions.sql',
        'payment_transactions.sql',
        'purchase_items_log.sql',
        'referals.sql',
        'referals_profit.sql',
        'bonuses.sql',
        'bonuses_items.sql',
        'bonus_codes.sql',
        'bonus_codes_activated_logs.sql',
        'shop_categories.sql',
        'shop_items.sql',
        'shop_items_packs.sql',
        'migration.sql',
        'all_items.sql',
		'gift_codes.sql',
		'gift_codes_attempts.sql',
    ];

    public function supportsTransaction(): bool
    {
        return false;
    }

    public function safeUp(): void
    {
        /* ➜ гарантируем кодировку перед любыми запросами */
        $this->execute("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");

        @set_time_limit(0);
        @ini_set('memory_limit', '-1');

        $dir = __DIR__ . '/../sql/';

        foreach ($this->order as $file) {
            $path = $dir . $file;
            if (!is_file($path)) {
                echo "Ошибка! Файл не найден: {$path}\n";
                continue;
            }

            echo "Выполняю файл: {$file}\n";

            $sql = file_get_contents($path);
            if ($sql === false) {
                echo "Ошибка! Не удалось прочитать файл: {$file}\n";
                continue;
            }

            $executed = false;
            $mysqliErr = null;

            if (extension_loaded('mysqli')) {
                try {
                    $db = $this->db;
                    $dsn = $db->dsn ?? '';
                    $username = $db->username ?? '';
                    $password = $db->password ?? '';
                    $host = '127.0.0.1';
                    $port = 3306;
                    $dbname = null;

                    if (preg_match('/host=([^;]+)/', $dsn, $m)) $host = $m[1];
                    if (preg_match('/port=([^;]+)/', $dsn, $m)) $port = (int)$m[1];
                    if (preg_match('/dbname=([^;]+)/', $dsn, $m)) $dbname = $m[1];

                    if (!$dbname && isset($db->attributes) && !empty($db->attributes['dbname'])) {
                        $dbname = $db->attributes['dbname'];
                    }

                    $mysqli = @new \mysqli($host, $username, $password, $dbname, $port);
                    if ($mysqli && $mysqli->connect_errno) {
                        $mysqliErr = $mysqli->connect_error;
                    } elseif ($mysqli) {
                        /* ➜ гарантируем кодировку и для mysqli */
                        $mysqli->set_charset('utf8mb4');
                        if ($mysqli->multi_query($sql)) {
                            do {
                                if ($result = $mysqli->store_result()) {
                                    $result->free();
                                }
                            } while ($mysqli->more_results() && $mysqli->next_result());
                            if ($mysqli->errno) {
                                $mysqliErr = $mysqli->error;
                            } else {
                                echo "Ок. {$file} выполнен (mysqli::multi_query).\n";
                                $executed = true;
                            }
                        } else {
                            $mysqliErr = $mysqli->error ?: 'multi_query вернул false';
                        }
                        $mysqli->close();
                    }
                } catch (\Throwable $e) {
                    $mysqliErr = $e->getMessage();
                }
            }

            if ($executed) {
                continue;
            }

            if ($mysqliErr !== null) {
                echo "Ошибка! mysqli::multi_query не удался для {$file}: {$mysqliErr}\n";
            }

            $stmts = $this->splitSqlStatements($sql);
            $count = 0;
            foreach ($stmts as $stmt) {
                $stmt = trim($stmt);
                if ($stmt === '' || preg_match('/^\s*--/', $stmt)) continue;
                try {
                    $this->execute($stmt);
                    $count++;
                } catch (\Throwable $e) {
                    echo "Ошибка! при выполнении запроса из {$file}: " . $e->getMessage() . "\n";
                }
            }
            echo "Ок. {$file} ({$count} команд выполнено)\n";
        }
    }

    private function splitSqlStatements(string $sql): array
    {
        $statements = [];
        $len = strlen($sql);
        $current = '';
        $inString = false;
        $stringChar = '';
        $prev = '';
        for ($i = 0; $i < $len; $i++) {
            $ch = $sql[$i];
            $current .= $ch;
            if ($inString) {
                if ($ch === $stringChar && $prev !== '\\') {
                    $inString = false;
                    $stringChar = '';
                }
            } else {
                if ($ch === '\'' || $ch === '"' || $ch === '`') {
                    $inString = true;
                    $stringChar = $ch;
                } elseif ($ch === ';') {
                    $statements[] = substr($current, 0, -1);
                    $current = '';
                }
            }
            $prev = $ch;
        }
        if (trim($current) !== '') {
            $statements[] = $current;
        }
        return $statements;
    }

    public function safeDown(): bool
    {
        echo "Откат невозможен — таблицы создавались вручную.\n";
        return false;
    }
}