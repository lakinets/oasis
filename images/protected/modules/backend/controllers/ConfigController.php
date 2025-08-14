<?php
namespace app\modules\backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;

use app\modules\backend\models\ConfigGroup;
use app\modules\backend\models\Config;

class ConfigController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules'  => [
                    ['allow' => true, 'roles' => ['@']],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $groups = ConfigGroup::find()->opened()->orderBy(['order' => SORT_ASC])->with('configs')->all();

        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post('Config', []);

            foreach ($post as $param => $value) {
                Yii::$app->db->createCommand()
                    ->update('config', [
                        'value'      => $value,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ], ['param' => $param])
                    ->execute();
            }

            Yii::$app->session->setFlash('success', 'Настройки сохранены.');
            return $this->redirect(['index']);
        }

        return $this->render('index', ['groups' => $groups]);
    }

    /* AJAX-сортировка строк config внутри группы */
    public function actionSort()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $data = Yii::$app->request->post('data');
        if (!$data) {
            return ['status' => 'fail', 'message' => 'No data'];
        }

        foreach (explode(',', $data) as $v) {
            [$id, $order] = array_map('intval', explode('-', $v));
            Yii::$app->db->createCommand()
                ->update('config', ['order' => $order], ['id' => $id])
                ->execute();
        }

        return ['status' => 'success'];
    }
}