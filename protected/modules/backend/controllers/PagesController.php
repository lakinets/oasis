<?php
namespace app\modules\backend\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;

use app\modules\backend\models\Pages;
use app\modules\backend\models\PagesSearch;

/**
 * Управление статическими страницами.
 * Наследует BackendController => вход только для admin.
 */
class PagesController extends BackendController
{
    /* ---------- 1. Список страниц ---------- */
    public function actionIndex()
    {
        $searchModel  = new PagesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel'  => $searchModel,
        ]);
    }

    /* ---------- 2. Добавление / редактирование ---------- */
    public function actionForm($id = null)
    {
        $model = $id ? $this->findModel($id) : new Pages();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                $now = date('Y-m-d H:i:s');
                if ($model->isNewRecord) {
                    $model->created_at = $now;
                } else {
                    $model->updated_at = $now;
                }

                if ($model->save(false)) {
                    Yii::$app->session->setFlash('success', $id
                        ? Yii::t('backend', 'Страница сохранена.')
                        : Yii::t('backend', 'Страница добавлена.')
                    );
                    return $this->redirect(['index']);
                }
                Yii::$app->session->setFlash('error', 'Не удалось сохранить страницу.');
            } else {
                Yii::$app->session->setFlash('error', implode('<br>', array_map(function ($v) {
                    return implode(', ', $v);
                }, $model->getErrors())));
            }
        }

        return $this->render('form', ['model' => $model]);
    }

    /* ---------- 3. Переключение статуса ---------- */
    public function actionAllow($id)
    {
        $model = $this->findModel($id);
        $model->status = $model->status ? 0 : 1;
        $model->save(false, ['status']);

        Yii::$app->session->setFlash('success', Yii::t('backend', 'Статус изменён.'));
        return $this->redirect(['index']);
    }

    /* ---------- 4. Удаление (soft delete) ---------- */
    public function actionDel($id)
    {
        $model = Pages::findOne($id);
        if (!$model) {
            Yii::$app->session->setFlash('error', 'Страница не найдена.');
            return $this->redirect(['index']);
        }

        $model->status = Pages::STATUS_DELETED;
        if ($model->save(false, ['status'])) {
            Yii::$app->session->setFlash('success', 'Страница удалена.');
        } else {
            Yii::$app->session->setFlash('error', 'Не удалось удалить страницу.');
        }
        return $this->redirect(['index']);
    }

    /* ---------- 5. Поиск модели ---------- */
    protected function findModel($id)
    {
        if (($model = Pages::find()
                ->andWhere(['status' => [Pages::STATUS_ON, Pages::STATUS_OFF]])
                ->andWhere(['id' => $id])
                ->one()) === null) {
            throw new NotFoundHttpException('Страница не найдена.');
        }
        return $model;
    }
}