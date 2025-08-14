<?php

namespace app\widgets\TopPk;

use yii\base\Widget;
use yii\helpers\ArrayHelper;
use app\models\Gs;

class TopPkWidget extends Widget
{
    public function run()
    {
        $data = $this->getTopPkData();
        return $this->render('top-pk', ['data' => $data]);
    }

    private function getTopPkData()
    {
        $cacheKey = 'top-pk';
        $data = \Yii::$app->cache->get($cacheKey);

        if ($data === false) {
            try {
                $allow = ArrayHelper::getValue(\Yii::$app->params, 'top.pk.allow', true);
                $gsId = (int) ArrayHelper::getValue(\Yii::$app->params, 'top.pk.gs_id', 1);
                $cacheTime = (int) ArrayHelper::getValue(\Yii::$app->params, 'top.pk.cache', 5);

                if ($allow && $gsId > 0) {
                    $gs = Gs::find()->where(['id' => $gsId])->one();
                    if ($gs) {
                        $dsn = "mysql:host={$gs->db_host};port={$gs->db_port};dbname={$gs->db_name}";
                        $db = new \yii\db\Connection([
                            'dsn' => $dsn,
                            'username' => $gs->db_user,
                            'password' => $gs->db_pass,
                            'charset' => 'utf8',
                        ]);
                        $db->open();

                        $data = $db->createCommand("
                            SELECT char_name, pkkills
                            FROM characters
                            WHERE accesslevel = 0
                            ORDER BY pkkills DESC
                            LIMIT 10
                        ")->queryAll();

                        if ($cacheTime > 0) {
                            \Yii::$app->cache->set($cacheKey, $data, $cacheTime * 60);
                        }
                    }
                }
            } catch (\Throwable $e) {
                \Yii::error('TopPkWidget error: ' . $e->getMessage());
                $data = [];
            }
        }

        return $data;
    }
}