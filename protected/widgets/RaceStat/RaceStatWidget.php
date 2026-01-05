<?php
namespace app\widgets\RaceStat;

use yii\base\Widget;
use yii\db\Connection;
use app\models\Gs;
use app\l2j\Pwsoft_it;

class RaceStatWidget extends Widget
{
    public function run()
    {
        /* 1. активный сервер */
        $gs = Gs::find()
            ->where(['status' => 1])
            ->orderBy(['id' => SORT_ASC])
            ->one();

        if (!$gs) {
            return $this->render('race-stat', ['stats' => $this->stub()]);
        }

        /* 2. подключение */
        try {
            $db = new Connection([
                'dsn'      => "mysql:host={$gs->db_host};port={$gs->db_port};dbname={$gs->db_name}",
                'username' => $gs->db_user,
                'password' => $gs->db_pass,
                'charset'  => 'utf8',
            ]);

            /* 3. статистика по расам */
            $raw = $db->createCommand("
                SELECT race, COUNT(*) AS cnt
                FROM characters
                WHERE accesslevel = 0
                GROUP BY race
            ")->queryAll();

            $total = (int)$db->createCommand(
                'SELECT COUNT(*) FROM characters WHERE accesslevel = 0'
            )->queryScalar();

            /* 4. гарантируем 5 рас (0-4) */
            $index = array_column($raw, 'cnt', 'race');
            $stats = [];
            foreach (range(0, 4) as $id) {
                $raceName = $this->raceName($id);
                $cnt      = $index[$id] ?? 0;
                $pct      = $total ? round($cnt * 100 / $total) : 0;
                $stats[]  = [
                    'race'    => $raceName,
                    'percent' => $pct,
                    'color'   => $this->raceColor($raceName),
                ];
            }
        } catch (\Throwable $e) {
            \Yii::info('RaceStat live error: ' . $e->getMessage());
            $stats = $this->stub();
        }

        return $this->render('race-stat', ['stats' => $stats]);
    }

    /* ---------- вспомогательные методы ---------- */
    private function raceName(int $id): string
    {
        return match ($id) {
            0 => 'Люди',
            1 => 'Светлые Эльфы',
            2 => 'Темные Эльфы',
            3 => 'Орки',
            4 => 'Гномы',
            default => 'Неизвестно',
        };
    }

    private function raceColor(string $name): string
    {
        return match ($name) {
            'Люди'          => 'yellow',
            'Светлые Эльфы' => 'yellow',
			'Темные Эльфы' => 'yellow',
			'Орки' => 'yellow',
			'Гномы' => 'yellow',
            default         => 'red',
        };
    }

    private function stub(): array
    {
        return [
            ['race' => 'Люди',          'percent' => 47, 'color' => 'yellow'],
            ['race' => 'Светлые Эльфы', 'percent' => 23, 'color' => 'yellow'],
            ['race' => 'Темные Эльфы',  'percent' => 11, 'color' => 'yellow'],
            ['race' => 'Орки',          'percent' => 9,  'color' => 'yellow'],
            ['race' => 'Гномы',         'percent' => 10, 'color' => 'yellow'],
        ];
    }
}