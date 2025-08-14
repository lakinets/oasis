<?php

namespace app\widgets\TopPvp;

use yii\base\Widget;
use yii\db\Connection;
use app\models\Gs;

class TopPvpWidget extends Widget
{
    public function run()
    {
        $data = $this->getTopPvpData();
        return $this->render('top-pvp', ['data' => $data]);
    }

    private function getTopPvpData()
    {
        $cacheKey = 'top-pvp-stats';
        $data = \Yii::$app->cache->get($cacheKey);

        if ($data === false) {
            try {
                $gs = Gs::find()->where(['id' => 1])->one();

                if ($gs) {
                    $dsn = "mysql:host={$gs->db_host};port={$gs->db_port};dbname={$gs->db_name}";
                    $db = new Connection([
                        'dsn' => $dsn,
                        'username' => $gs->db_user,
                        'password' => $gs->db_pass,
                        'charset' => 'utf8',
                    ]);

                    $data = $db->createCommand("
                        SELECT char_name, pvpkills
                        FROM characters
                        WHERE accesslevel = 0
                        ORDER BY pvpkills DESC
                        LIMIT 10
                    ")->queryAll();

                    \Yii::$app->cache->set($cacheKey, $data, 300);
                } else {
                    $data = [];
                }
            } catch (\Throwable $e) {
                \Yii::error('TopPvpWidget error: ' . $e->getMessage());
                $data = [];
            }
        }

        return $data;
    }
}
