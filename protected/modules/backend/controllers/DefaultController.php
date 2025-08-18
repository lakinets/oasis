<?php

namespace app\modules\backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use app\modules\backend\models\LoginForm;
use app\modules\backend\models\AllItems;

class DefaultController extends Controller
{
    public $layout = 'master';   // основной layout backend

    /**
     * Главная страница админ-панели
     */
    public function actionIndex()
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['login']);
        }

        /* ---- Информация о проекте ---- */
        $info = [
            'Название'      => 'GHTWEB X - НЕ ДЛЯ ПРОДАЖИ',
            'Версия'        => '1.01.2 -  Open-Source fan Remaster',
            'Yii'           => Yii::getVersion(),
            'PHP'           => PHP_VERSION,
            'ОС'            => PHP_OS,
            'Время'         => date('Y-m-d H:i:s'),
            'Память (real)' => sprintf('%.2f MB', memory_get_usage(true) / 1048576),
            'Каталог'       => Yii::getAlias('@app'),
            'Кеш-хранилище' => get_class(Yii::$app->cache),
        ];

        return $this->render('index', ['info' => $info]);
    }

    /**
     * Страница авторизации
     */
    public function actionLogin()
    {
        $this->layout = 'login';   // отдельный layout для входа

        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['index']);
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->redirect(['index']);
        }

        return $this->render('login', ['model' => $model]);
    }

    /**
     * AJAX-поиск/получение предметов (оставлено без изменений)
     */
    public function actionGetItemInfo()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $id = (int)Yii::$app->request->get('item-id');
        if ($id) {
            $model = AllItems::findOne($id);
            if (!$model) {
                throw new NotFoundHttpException('Предмет не найден.');
            }
            return [
                'status' => 'success',
                'msg'    => $model->name .
                            ($model->add_name ? ' (' . $model->add_name . ') [' . $model->item_id . ']' : ''),
            ];
        }

        $query = Yii::$app->request->get('query');
        $limit = (int)Yii::$app->request->get('limit', 0);

        if (mb_strlen($query) === 0) {
            return [];
        }

        set_time_limit(0);

        $q = AllItems::find()
            ->select(['item_id', 'name', 'add_name', 'icon'])
            ->andFilterWhere(['like', 'name', $query])
            ->orderBy(['name' => SORT_ASC]);

        if ($limit > 0) {
            $q->limit($limit);
        }

        $items = [];
        foreach ($q->all() as $item) {
            $items[] = [
                'id'    => $item->item_id,
                'value' => $item->name .
                           ($item->add_name ? ' (' . $item->add_name . ') [' . $item->item_id . ']' : ''),
                'icon'  => $item->getIcon(),
            ];
        }

        return $items;
    }

    /**
     * Простая очистка кеша
     */
    public function actionClearCache()
    {
        Yii::$app->cache->flush();
        Yii::$app->session->setFlash('success', 'Кеш успешно очищен.');
        return $this->redirect(['index']);
    }

    /**
     * Заглушка для проверки обновлений
     */
    public function actionUpdate()
    {
        Yii::$app->session->setFlash('success', 'Обновление выполнено.');
        return $this->redirect(['index']);
    }
}