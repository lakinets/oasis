<?php
namespace app\modules\backend\controllers;

use Yii;
use app\modules\backend\models\ConfigGroup;
use app\modules\backend\models\Config;

class ConfigController extends BackendController
{
    public function actionIndex()
    {
        $groups = ConfigGroup::findOpened()
            ->orderBy(['order' => SORT_ASC])
            ->with('configs')
            ->all();

        if (Yii::$app->request->isPost) {
            $postData = Yii::$app->request->post('Config', []);
            if (!empty($postData)) {
                foreach ($postData as $id => $row) {
                    $config = Config::findOne($id);
                    if ($config !== null && isset($row['value'])) {
                        $val = $row['value'];
                        
                        // Если это чекбоксы (массив) — сохраняем как JSON
                        if (is_array($val)) {
                            $val = json_encode($val);
                        }

                        $config->value = $val;
                        $config->save(false, ['value']);
                    }
                }
                Yii::$app->session->setFlash('success', Yii::t('backend', 'Настройки успешно сохранены.'));
            }
            return $this->refresh();
        }

        return $this->render('index', [
            'groups' => $groups,
        ]);
    }
}