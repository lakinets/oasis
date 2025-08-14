<?php
namespace app\modules\backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\modules\backend\models\LoginServers;
use app\modules\backend\models\LoginServersSearch;

class LoginServersController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class'   => VerbFilter::class,
                'actions' => [
                    'del' => ['POST'],  // исправлено: actionDel - HTTP POST
                ],
            ],
        ];
    }

    /**
     * Список логин-серверов
     */
    public function actionIndex()
    {
        $searchModel  = new LoginServersSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Форма добавления/редактирования логин-сервера
     * @param int|null $id
     * @return string|\yii\web\Response
     */
    public function actionForm($id = null)
    {
        if ($id !== null) {
            $model = $this->findModel($id);
        } else {
            $model = new LoginServers();
        }

        // Получаем список версий из папки components/versions
        $versionsDir = Yii::getAlias('@app/modules/backend/components/versions');
        $files = scandir($versionsDir);
        $versions = [];

        foreach ($files as $file) {
            if (is_file($versionsDir . DIRECTORY_SEPARATOR . $file) && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                $versions[pathinfo($file, PATHINFO_FILENAME)] = pathinfo($file, PATHINFO_FILENAME);
            }
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash(
                'success',
                $id
                    ? Yii::t('backend', 'Изменения сохранены.')
                    : Yii::t('backend', 'Логин-сервер добавлен.')
            );
            return $this->redirect(['index']);
        }

        return $this->render('form', [
            'model'    => $model,
            'versions' => $versions,
        ]);
    }

    /**
     * Включение/выключение логин-сервера
     * @param int $id
     * @return \yii\web\Response
     */
    public function actionAllow($id)
    {
        $model = $this->findModel($id);
        $model->status = $model->status == LoginServers::STATUS_ON ? LoginServers::STATUS_OFF : LoginServers::STATUS_ON;
        $model->save(false);
        return $this->redirect(['index']);
    }

    /**
     * Удаление логин-сервера
     * @param int $id
     * @return \yii\web\Response
     */
    public function actionDel($id)
    {
        $this->findModel($id)->delete();
        Yii::$app->session->setFlash('success', Yii::t('backend', 'Сервер удалён.'));
        return $this->redirect(['index']);
    }

    /**
     * Поиск модели по ID
     * @param int $id
     * @return LoginServers
     * @throws NotFoundHttpException
     */
    protected function findModel($id): LoginServers
    {
        if (($model = LoginServers::findOne((int)$id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException(Yii::t('backend', 'Сервер не найден.'));
    }
}
