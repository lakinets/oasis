<?php
namespace app\modules\backend\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;

use app\modules\backend\models\News;
use app\modules\backend\models\NewsSearch;

/**
 * Контроллер управления новостями.
 * Наследует BackendController, поэтому вход только для admin.
 */
class NewsController extends BackendController
{
    /* ---------- 1. Список новостей ---------- */
    public function actionIndex()
    {
        $searchModel  = new NewsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel'  => $searchModel,
        ]);
    }

    /* ---------- 2. Добавление / редактирование ---------- */
    public function actionForm($id = null)
    {
        $model = $id ? $this->findModel($id) : new News();

        if ($model->load(Yii::$app->request->post())) {
            
            // Если это новая запись, подставляем ID текущего админа
            if ($model->isNewRecord) {
                $model->user_id = Yii::$app->user->id;
                $model->created_at = date('Y-m-d H:i:s');
            } else {
                $model->updated_at = date('Y-m-d H:i:s');
            }

            if ($model->save()) {
                Yii::$app->session->setFlash('success', $id
                    ? Yii::t('backend', 'Новость сохранена.')
                    : Yii::t('backend', 'Новость добавлена.')
                );
                return $this->redirect(['index']);
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
        $model = $this->findModel($id);
        $model->status = News::STATUS_DELETED;
        $model->save(false, ['status']);

        Yii::$app->session->setFlash('success', Yii::t('backend', 'Новость удалена.'));
        return $this->redirect(['index']);
    }

    /* ---------- 5. Поиск модели ---------- */
    protected function findModel($id)
    {
        if (($model = News::find()
                ->andWhere(['!=', 'status', News::STATUS_DELETED])
                ->andWhere(['id' => $id])
                ->one()) === null) {
            throw new NotFoundHttpException(Yii::t('backend', 'Новость не найдена.'));
        }
        return $model;
    }

    /* ---------- 6. Загрузка картинок для WYSIWYG ---------- */
    public function actionUploadImage()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (!Yii::$app->request->isPost) {
            return ['success' => false, 'message' => 'Неверный метод запроса.'];
        }

        /** @var \yii\web\UploadedFile $file */
        $file = \yii\web\UploadedFile::getInstanceByName('file');

        if ($file) {
            $ext = strtolower($file->extension);
            
            if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                return ['success' => false, 'message' => 'Неподдерживаемый формат.'];
            }

            if ($file->size > 5 * 1024 * 1024) {
                return ['success' => false, 'message' => 'Файл слишком большой (максимум 5 МБ).'];
            }

            $dir = Yii::getAlias('@webroot/uploads/images/news/');
            
            if (!is_dir($dir)) {
                \yii\helpers\FileHelper::createDirectory($dir, 0775, true);
            }
            
            $name = uniqid('news_') . '.' . $ext;
            
            if ($file->saveAs($dir . $name)) {
                return [
                    'success' => true, 
                    'file' => '/uploads/images/news/' . $name
                ];
            }
        }
        
        return [
            'success' => false, 
            'message' => 'Ошибка сервера при сохранении файла.'
        ];
    }
}