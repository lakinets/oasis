<?php
namespace app\modules\backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;

use app\modules\backend\models\Bonuses;
use app\modules\backend\models\BonusesItems;
use app\modules\backend\models\BonusCodes;
use app\modules\backend\models\BonusCodesActivatedLogs;

class BonusesController extends Controller
{
    /* ------------------------------------------------------------------ */
    /* Бонусы                                                             */
    /* ------------------------------------------------------------------ */

    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Bonuses::find()
                ->orderBy(['title' => SORT_ASC])
                ->with(['items', 'itemCount']),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionForm($id = null)
    {
        $model = $id ? $this->findBonusesModel($id) : new Bonuses();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', $id
                ? Yii::t('backend', 'Бонус сохранен.')
                : Yii::t('backend', 'Бонус добавлен.'));
            return $this->redirect(['index']);
        }

        return $this->render('form', [
            'model' => $model,
        ]);
    }

    public function actionAllow($id)
    {
        $model = $this->findBonusesModel($id);
        $model->status = $model->status ? 0 : 1;
        $model->save(false, ['status']);

        Yii::$app->session->setFlash('success', Yii::t('backend', 'Статус изменен на <b>:status</b>.', [
            ':status' => $model->getStatusLabel(),
        ]));
        return $this->redirect(['index']);
    }

    public function actionDel($id)
    {
        $model = $this->findBonusesModel($id);
        $model->delete();
        Yii::$app->session->setFlash('success', Yii::t('backend', 'Бонус <b>:name</b> удален', [
            ':name' => Html::encode($model->title),
        ]));
        return $this->redirect(['index']);
    }

    /* ------------------------------------------------------------------ */
    /* Предметы в бонусе                                                  */
    /* ------------------------------------------------------------------ */

    public function actionItems($bonus_id)
    {
        $bonus = $this->findBonusesModel($bonus_id);

        $dataProvider = new ActiveDataProvider([
            'query' => BonusesItems::find()
                ->where(['bonus_id' => $bonus_id])
                ->with(['itemInfo']),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('items/index', [
            'bonus'        => $bonus,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionItemAdd($bonus_id)
    {
        $model = new BonusesItems(['bonus_id' => $bonus_id]);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('backend', 'Предмет добавлен.'));
            return $this->redirect(['items', 'bonus_id' => $bonus_id]);
        }

        return $this->render('items/form', [
            'bonus' => $this->findBonusesModel($bonus_id),
            'model' => $model,
        ]);
    }

    public function actionItemEdit($bonus_id, $item_id)
    {
        $model = BonusesItems::findOne($item_id);
        if (!$model) {
            throw new NotFoundHttpException(Yii::t('backend', 'Предмет не найден.'));
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('backend', 'Предмет сохранен.'));
            return $this->redirect(['items', 'bonus_id' => $bonus_id]);
        }

        return $this->render('items/form', [
            'bonus' => $this->findBonusesModel($bonus_id),
            'model' => $model,
        ]);
    }

    public function actionItemAllow($bonus_id, $item_id)
    {
        $model = $this->findBonusesItemModel($item_id);
        $model->status = $model->status ? 0 : 1;
        $model->save(false, ['status']);

        Yii::$app->session->setFlash('success', Yii::t('backend', 'Статус изменен.'));
        return $this->redirect(['items', 'bonus_id' => $bonus_id]);
    }

    public function actionItemDel($bonus_id, $item_id)
    {
        $model = $this->findBonusesItemModel($item_id);
        $model->delete();
        Yii::$app->session->setFlash('success', Yii::t('backend', 'Предмет удален.'));
        return $this->redirect(['items', 'bonus_id' => $bonus_id]);
    }

    /* ------------------------------------------------------------------ */
    /* Коды для бонусов                                                   */
    /* ------------------------------------------------------------------ */

    public function actionCodes()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => BonusCodes::find()->with(['bonusInfo', 'bonusLog']),
            'pagination' => ['pageSize' => 20],
        ]);

        return $this->render('codes/index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCodeAdd()
    {
        $model = new BonusCodes(['scenario' => 'code_form']);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('backend', 'Код добавлен.'));
            return $this->redirect(['codes']);
        }

        return $this->render('codes/form', ['model' => $model]);
    }

    public function actionCodeEdit($code_id)
    {
        $model = $this->findBonusCodesModel($code_id);
        $model->scenario = 'form_code';

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('backend', 'Изменения сохранены.'));
            return $this->redirect(['codes']);
        }

        return $this->render('codes/form', ['model' => $model]);
    }

    public function actionCodeDel($code_id)
    {
        $model = $this->findBonusCodesModel($code_id);
        $model->delete();
        Yii::$app->session->setFlash('success', Yii::t('backend', 'Код удален.'));
        return $this->redirect(['codes']);
    }

    public function actionCodeAllow($code_id)
    {
        $model = $this->findBonusCodesModel($code_id);
        $model->status = $model->status ? 0 : 1;
        $model->save(false, ['status']);

        Yii::$app->session->setFlash('success', Yii::t('backend', 'Статус изменен.'));
        return $this->redirect(['codes']);
    }

    /* ------------------------------------------------------------------ */
    /* Генерация кода                                                     */
    /* ------------------------------------------------------------------ */

    public function actionGenerateCode($parts = 4, $length = 4, $divider = '-')
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $code = '';
        for ($i = 0; $i < $parts; $i++) {
            $code .= strtoupper(Yii::$app->security->generateRandomString($length)) . $divider;
        }
        return substr($code, 0, -1);
    }

    /* ------------------------------------------------------------------ */
    /* Поиск моделей                                                      */
    /* ------------------------------------------------------------------ */

    protected function findBonusesModel($id)
    {
        if (($model = Bonuses::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Бонус не найден.');
    }

    protected function findBonusesItemModel($id)
    {
        if (($model = BonusesItems::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Предмет не найден.');
    }

    protected function findBonusCodesModel($id)
    {
        if (($model = BonusCodes::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Код не найден.');
    }
}