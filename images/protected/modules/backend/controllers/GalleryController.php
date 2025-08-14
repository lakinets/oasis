<?php
namespace app\modules\backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;
use yii\web\UploadedFile;
use app\modules\backend\models\Gallery;

class GalleryController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    ['allow' => true, 'roles' => ['@']],
                ],
            ],
        ];
    }

    /**
     * Список картинок
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Gallery::find()->orderBy(['sort' => SORT_ASC]),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Добавить / редактировать картинку
     * @param int|null $id
     */
    public function actionForm($id = null)
    {
        $model = $id ? $this->findModel($id) : new Gallery();

        if ($model->load(Yii::$app->request->post())) {
            // пример загрузки файла
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
            if ($model->save()) {
                Yii::$app->session->setFlash('success',
                    $id ? Yii::t('backend', 'Изменения сохранены.')
                        : Yii::t('backend', 'Картинка добавлена.')
                );
                return $this->redirect(['index']);
            }
        }

        return $this->render('form', ['model' => $model]);
    }

    /**
     * Переключение статуса
     */
    public function actionAllow($id)
    {
        $model = $this->findModel($id);
        $model->status = $model->status ? 0 : 1;
        $model->save(false, ['status']);

        Yii::$app->session->setFlash('success',
            Yii::t('backend', 'Статус изменен на <b>:status</b>.', [
                ':status' => $model->getStatusLabel(),
            ])
        );
        return $this->redirect(['index']);
    }

    /**
     * Удаление
     */
    public function actionDel($id)
    {
        $model = $this->findModel($id);
        $model->delete();
        Yii::$app->session->setFlash('success',
            Yii::t('backend', 'Картинка удалена, ID :id', [':id' => $id])
        );
        return $this->redirect(['index']);
    }

    /**
     * Поиск модели по PK
     */
    protected function findModel($id)
    {
        if (($model = Gallery::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}