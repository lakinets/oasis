<?php
namespace app\modules\backend\controllers;

use Yii;
use app\modules\backend\models\ConfigGroup;
use app\modules\backend\models\Config;
use yii\web\Controller;
use yii\web\Response;
use yii\web\NotFoundHttpException;

class ConfigController extends Controller
{
    public function actionIndex()
    {
        $groups = ConfigGroup::findOpened()
            ->orderBy(['order' => SORT_ASC])
            ->with('configs')
            ->all();

        // Если POST — сохраняем
        if (Yii::$app->request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $postData = Yii::$app->request->post('Config', []);
            if (!empty($postData)) {
                foreach ($postData as $id => $row) {
                    $config = Config::findOne((int)$id);
                    if ($config !== null && isset($row['value'])) {
                        $config->value = $row['value'];
                        $config->save(false, ['value']);
                    }
                }
                return ['success' => true, 'message' => 'Настройки успешно сохранены'];
            }

            return ['success' => false, 'message' => 'Нет данных для сохранения'];
        }

        return $this->render('index', [
            'groups' => $groups,
        ]);
    }
}
