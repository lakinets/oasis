<?php
namespace app\modules\backend\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;
use app\modules\backend\models\Users;
use app\modules\backend\models\EditUserForm;

class UsersController extends BackendController 
{
    /**
     * Список пользователей.
     */
    public function actionIndex()
    {
        $searchModel  = new Users();
        $query        = Users::find()->joinWith(['profile']);

        // фильтрация по GET-параметрам
        if (($login = Yii::$app->request->get('login')) !== null && $login !== '') {
            $query->andWhere(['like', 'users.login', $login]);
        }
        if (($role = Yii::$app->request->get('role')) !== null && $role !== '') {
            $query->andWhere(['users.role' => $role]);
        }

        $dataProvider = new ActiveDataProvider([
            'query'      => $query,
            'pagination' => ['pageSize' => 20],
            'sort'       => [
                'defaultOrder' => ['user_id' => SORT_DESC],
                'attributes'   => ['user_id', 'login', 'email', 'role', 'activated'],
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel'  => $searchModel,
        ]);
    }

    /**
     * Просмотр пользователя.
     */
    public function actionView($user_id)
    {
        $user = Users::findOne($user_id);
        if (!$user) {
            throw new NotFoundHttpException('Пользователь не найден');
        }
        return $this->render('view', ['user' => $user]);
    }

    /**
     * Редактирование пользователя.
     */
    public function actionEditData($user_id)
    {
        $user = Users::findOne($user_id);
        if (!$user) {
            throw new NotFoundHttpException('Пользователь не найден');
        }

        $model = new EditUserForm();
        $model->loadFromUser($user);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->applyToUser($user)) {
                Yii::$app->session->setFlash('success', 'Пользователь обновлён');
                return $this->redirect(['view', 'user_id' => $user->user_id]);
            }
            Yii::$app->session->setFlash('error', 'Ошибка сохранения');
        }

        return $this->render('edit-data', [
            'model' => $model,
        ]);
    }
}