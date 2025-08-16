<?php
namespace app\modules\backend\controllers;

use Yii;
use app\modules\backend\models\ConfigGroup;
use app\modules\backend\models\Config;

class ConfigController extends BackendController   // <-- Наследуемся от BackendController
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
                        $config->value = $row['value'];
                        $config->save(false, ['value']);
                    }
                }
                Yii::$app->session->setFlash('success', 'Настройки успешно сохранены.');
            }
            return $this->refresh();
        }

        return $this->render('index', [
            'groups' => $groups,
        ]);
    }

    // Остальные методы (actionCreate, actionUpdate, ...) остаются без изменений.
}