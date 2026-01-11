<?php
namespace app\modules\cabinet\controllers;

use Yii;
use app\models\Gs;
use app\models\Services;
use app\models\UserProfiles;
use app\l2j\DriverFactory;

/* Подключаем все необходимые формы */
use app\models\forms\ChangeCharNameForm;
use app\models\forms\ChangeCharGenderForm;
use app\models\forms\RemoveKarmaForm;
use app\models\forms\NobleStatusForm;
use app\models\forms\GiftCodeCreateForm;
use app\models\forms\GiftCodeActivateForm;

class ServicesController extends CabinetBaseController
{
    /* ---------- СПИСОК УСЛУГ ---------- */
    public function actionIndex()
    {
        $services = Services::getActiveServices();
        $profile  = UserProfiles::findOne(['user_id' => Yii::$app->user->id]);
        $servers  = Gs::getOpenServers();

        return $this->render('index', [
            'services' => $services,
            'profile'  => $profile,
            'servers'  => $servers,
        ]);
    }

    /* ---------- ПОДАРОЧНЫЙ КОД ---------- */
    public function actionGiftCode()
    {
        $service = Services::findOne(['type' => Services::TYPE_GIFT_CODE]);
        if (!$service || $service->status != Services::STATUS_ENABLED) {
            Yii::$app->session->setFlash('error', 'Сервис «Подарочный код» временно недоступен.');
            return $this->redirect(['index']);
        }

        $userId  = Yii::$app->user->id;
        $profile = UserProfiles::findOne(['user_id' => $userId]);

        $createModel   = new GiftCodeCreateForm();
        $activateModel = new GiftCodeActivateForm();

        if ($createModel->load(Yii::$app->request->post())) {
            if ($createModel->validate() && $createModel->createCode($service, [10, 25, 50, 100, 300, 500, 1000])) {
                return $this->refresh();
            }
        }

        if ($activateModel->load(Yii::$app->request->post())) {
            if ($activateModel->validate() && $activateModel->activateCode()) {
                return $this->refresh();
            }
        }

        $myCodes = \app\models\GiftCode::find()
            ->where(['user_id' => $userId, 'status' => 'active'])
            ->orderBy(['id' => SORT_DESC])
            ->all();

        return $this->render('gift-code', [
            'createModel'   => $createModel,
            'activateModel' => $activateModel,
            'nominals'      => [10, 25, 50, 100, 300, 500, 1000],
            'cost'          => $service->cost,
            'balance'       => $profile->balance ?? 0,
            'myCodes'       => $myCodes,
        ]);
    }

    /* ---------- СМЕНА ИМЕНИ ---------- */
    public function actionChangeCharName(?int $gs_id = null)
    {
        $service = Services::findOne(['type' => Services::TYPE_CHANGE_NAME]);
        if (!$service || $service->status != Services::STATUS_ENABLED) {
            Yii::$app->session->setFlash('error', 'Сервис «Смена имени» временно недоступен.');
            return $this->redirect(['index']);
        }

        $servers = Gs::getOpenServers();
        if (!$servers) {
            Yii::$app->session->setFlash('error', 'Нет доступных игровых серверов.');
            return $this->redirect(['index']);
        }

        if ($gs_id === null) $gs_id = $servers[0]->id;
        $server = Gs::findOne($gs_id);

        try {
            $gameDb = $this->connectToGameDb($server);
        } catch (\Exception $e) {
            Yii::$app->session->setFlash('error', 'Ошибка БД игрового сервера.');
            return $this->redirect(['index']);
        }

        $driver = DriverFactory::make($gameDb, $server->version);
        $login  = Yii::$app->user->identity->login;

        $raw = $driver->charactersQuery()->where(['characters.account_name' => $login])->all($gameDb);
        $characters = []; $charMap = [];
        foreach ($raw as $row) {
            $cid = $row['char_id'] ?? $row['obj_Id'] ?? null;
            $cn  = $row['char_name'] ?? null;
            if ($cid) { $characters[] = ['char_id' => $cid, 'char_name' => $cn]; $charMap[$cid] = $cn; }
        }

        $profile = UserProfiles::findOne(['user_id' => Yii::$app->user->id]);
        $model = new ChangeCharNameForm();

        if ($model->load(Yii::$app->request->post()) && $model->executeChange($service, $gameDb, $driver)) {
            Yii::$app->session->setFlash('success', 'Имя успешно изменено!');
            return $this->redirect(['change-char-name', 'gs_id' => $gs_id]);
        }

        return $this->render('change-char-name', [
            'servers' => $servers, 'gs_id' => $gs_id, 'characters' => $characters, 
            'charMap' => $charMap, 'model' => $model, 'cost' => $service->cost, 'balance' => $profile->balance ?? 0
        ]);
    }

    /* ---------- СМЕНА ПОЛА ---------- */
    public function actionChangeGender(?int $gs_id = null)
    {
        $service = Services::findOne(['type' => Services::TYPE_CHANGE_GENDER]);
        if (!$service || $service->status != Services::STATUS_ENABLED) {
            Yii::$app->session->setFlash('error', 'Сервис «Смена пола» временно недоступен.');
            return $this->redirect(['index']);
        }

        $servers = Gs::getOpenServers();
        if (!$servers) {
            Yii::$app->session->setFlash('error', 'Нет доступных серверов.');
            return $this->redirect(['index']);
        }

        if ($gs_id === null) $gs_id = $servers[0]->id;
        $server = Gs::findOne($gs_id);

        try {
            $gameDb = $this->connectToGameDb($server);
        } catch (\Exception $e) {
            Yii::$app->session->setFlash('error', 'Ошибка подключения к БД игры.');
            return $this->redirect(['index']);
        }

        $driver = DriverFactory::make($gameDb, $server->version);
        $login  = Yii::$app->user->identity->login;

        $raw = $driver->charactersQuery()->where(['characters.account_name' => $login])->andWhere(['!=', 'characters.race', 7])->all($gameDb);
        $characters = []; $charMap = [];
        foreach ($raw as $row) {
            $cid = $row['char_id'] ?? $row['obj_Id'] ?? null;
            $cn = $row['char_name'] ?? null;
            $sex = $row['sex'] ?? 0;
            if ($cid) { $characters[] = ['char_id' => $cid, 'char_name' => $cn, 'sex' => $sex]; $charMap[$cid] = "$cn (" . ($sex ? 'Ж' : 'М') . ")"; }
        }

        $profile = UserProfiles::findOne(['user_id' => Yii::$app->user->id]);
        $model = new ChangeCharGenderForm();

        if ($model->load(Yii::$app->request->post()) && $model->executeChange($service, $gameDb, $driver)) {
            Yii::$app->session->setFlash('success', 'Пол изменен!');
            return $this->redirect(['change-gender', 'gs_id' => $gs_id]);
        }

        return $this->render('change-gender', [
            'servers' => $servers, 'gs_id' => $gs_id, 'characters' => $characters, 
            'charMap' => $charMap, 'model' => $model, 'cost' => $service->cost, 'balance' => $profile->balance ?? 0
        ]);
    }

    /* ---------- СНЯТИЕ КАРМЫ ---------- */
    public function actionRemoveKarma(?int $gs_id = null)
    {
        $service = Services::findOne(['type' => Services::TYPE_REMOVE_KARMA]);
        if (!$service || $service->status != Services::STATUS_ENABLED) {
            Yii::$app->session->setFlash('error', 'Сервис снятия кармы отключен.');
            return $this->redirect(['index']);
        }

        $servers = Gs::getOpenServers();
        if ($gs_id === null && $servers) $gs_id = $servers[0]->id;
        $server = Gs::findOne($gs_id);

        try {
            $gameDb = $this->connectToGameDb($server);
        } catch (\Exception $e) {
            Yii::$app->session->setFlash('error', 'Ошибка БД.');
            return $this->redirect(['index']);
        }

        $driver = DriverFactory::make($gameDb, $server->version);
        $raw = $driver->charactersQuery()->where(['characters.account_name' => Yii::$app->user->identity->login])->andWhere(['>', 'karma', 0])->all($gameDb);

        $characters = []; $charMap = [];
        foreach ($raw as $row) {
            $cid = $row['char_id'] ?? $row['obj_Id'] ?? null;
            $cn = $row['char_name'] ?? null;
            $karma = $row['karma'] ?? 0;
            if ($cid) { $characters[] = ['char_id' => $cid, 'karma' => $karma]; $charMap[$cid] = "$cn (Карма: $karma)"; }
        }

        $profile = UserProfiles::findOne(['user_id' => Yii::$app->user->id]);
        $model = new RemoveKarmaForm();

        if ($model->load(Yii::$app->request->post()) && $model->executeChange($service, $gameDb, $driver)) {
            Yii::$app->session->setFlash('success', 'Карма обнулена!');
            return $this->redirect(['remove-karma', 'gs_id' => $gs_id]);
        }

        return $this->render('remove-karma', [
            'servers' => $servers, 'gs_id' => $gs_id, 'characters' => $characters, 'charMap' => $charMap,
            'model' => $model, 'cost' => $service->cost, 'balance' => $profile->balance ?? 0
        ]);
    }

    /* ---------- СТАТУС ДВОРЯНИНА ---------- */
    public function actionNobleStatus(?int $gs_id = null)
    {
        $service = Services::findOne(['type' => Services::TYPE_NOBLE_STATUS]);
        if (!$service || $service->status != Services::STATUS_ENABLED) {
            Yii::$app->session->setFlash('error', 'Сервис недоступен.');
            return $this->redirect(['index']);
        }

        $servers = Gs::getOpenServers();
        if ($gs_id === null && $servers) $gs_id = $servers[0]->id;
        $server = Gs::findOne($gs_id);

        try {
            $gameDb = $this->connectToGameDb($server);
        } catch (\Exception $e) {
            Yii::$app->session->setFlash('error', 'Ошибка БД.');
            return $this->redirect(['index']);
        }

        $driver = DriverFactory::make($gameDb, $server->version);
        $raw = $driver->charactersQuery()->where(['characters.account_name' => Yii::$app->user->identity->login])->andWhere(['characters.nobless' => 0])->all($gameDb);

        $characters = []; $charMap = [];
        foreach ($raw as $row) {
            $cid = $row['char_id'] ?? $row['obj_Id'] ?? null;
            $cn = $row['char_name'] ?? null;
            if ($cid) { $characters[] = ['char_id' => $cid]; $charMap[$cid] = $cn; }
        }

        $profile = UserProfiles::findOne(['user_id' => Yii::$app->user->id]);
        $model = new NobleStatusForm();

        if ($model->load(Yii::$app->request->post()) && $model->executeChange($service, $gameDb, $driver)) {
            Yii::$app->session->setFlash('success', 'Статус дворянина получен!');
            return $this->redirect(['noble-status', 'gs_id' => $gs_id]);
        }

        return $this->render('noble-status', [
            'servers' => $servers, 'gs_id' => $gs_id, 'characters' => $characters, 'charMap' => $charMap,
            'model' => $model, 'cost' => $service->cost, 'balance' => $profile->balance ?? 0
        ]);
    }

    /* Вспомогательный метод для подключения к базе игрового сервера */
    private function connectToGameDb($server)
    {
        $db = new \yii\db\Connection([
            'dsn'      => "mysql:host={$server->db_host};port={$server->db_port};dbname={$server->db_name}",
            'username' => $server->db_user,
            'password' => $server->db_pass,
            'charset'  => 'utf8',
        ]);
        $db->open();
        return $db;
    }
}