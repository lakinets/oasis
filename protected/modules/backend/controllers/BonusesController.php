<?php
namespace app\modules\backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use app\modules\backend\models\Bonuses;
use app\modules\backend\models\BonusesSearch;
use app\modules\backend\models\BonusCodes;
use app\modules\backend\models\BonusCodesSearch;

class BonusesController extends Controller
{
    /* ------------ Бонусы ------------ */

    public function actionIndex()
    {
        $searchModel  = new BonusesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionForm($id = null)
    {
        $model = $id ? $this->findModel($id) : new Bonuses();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->date_end === '') {
                $model->date_end = null;
            }
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Сохранено.');
                return $this->redirect(['index']);
            }
            Yii::$app->session->setFlash('error', 'Ошибка при сохранении.');
        }

        return $this->render('form', ['model' => $model]);
    }

    public function actionAllow($id)
    {
        $model = $this->findModel($id);
        $model->status = $model->status ? 0 : 1;
        $model->save(false);
        return $this->redirect(['index']);
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        BonusCodes::deleteAll(['bonus_id' => $model->id]);
        $model->delete();
        return $this->redirect(['index']);
    }

    /* ------------ Коды бонусов ------------ */

    public function actionCodes($bonus_id = null)
    {
        $searchModel  = new BonusCodesSearch();
        $dataProvider = $searchModel->search(array_merge(
            Yii::$app->request->queryParams,
            ['BonusCodesSearch' => ['bonus_id' => $bonus_id]]
        ));

        return $this->render('codes/index', [
            'searchModel' => $searchModel,
            'dataProvider'=> $dataProvider,
            'bonus_id'    => $bonus_id,
        ]);
    }

    public function actionCodeForm($id = null, $bonus_id = null)
    {
        $model = $id ? BonusCodes::findOne($id) : new BonusCodes();
        if ($bonus_id !== null) {
            $model->bonus_id = $bonus_id;
        }

        if ($model->load(Yii::$app->request->post())) {
            if (!$model->code) {
                $model->code = $this->generateRandomCode();
            }
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Сохранено.');
                return $this->redirect(['codes', 'bonus_id' => $model->bonus_id]);
            }
            Yii::$app->session->setFlash('error', 'Ошибка при сохранении.');
        }

        return $this->render('codes/form', ['model' => $model]);
    }

    public function actionCodeToggle($id)
    {
        $model = BonusCodes::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('Код не найден.');
        }
        $model->status = $model->status ? 0 : 1;
        $model->save(false);
        return $this->redirect(['codes', 'bonus_id' => $model->bonus_id]);
    }

    public function actionCodeDelete($id)
    {
        $model = BonusCodes::findOne($id);
        if ($model) {
            $bonus_id = $model->bonus_id;
            $model->delete();
            return $this->redirect(['codes', 'bonus_id' => $bonus_id]);
        }
        return $this->redirect(['codes']);
    }

    public function actionGenerateCode()
    {
        Yii::$app->response->format = Response::FORMAT_RAW;
        return $this->generateRandomCode();
    }

    protected function generateRandomCode($len = 16)
    {
        $parts = [];
        for ($i = 0; $i < 4; $i++) {
            $parts[] = strtoupper(substr(bin2hex(random_bytes(2)), 0, 4));
        }
        return implode('-', $parts);
    }

    protected function findModel($id)
    {
        if (($model = Bonuses::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Бонус не найден.');
    }
}