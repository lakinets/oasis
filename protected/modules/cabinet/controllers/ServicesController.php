<?php
namespace app\modules\cabinet\controllers;

use Yii;
use app\models\Gs;
use app\models\Services;
use app\models\UserProfiles;
use app\l2j\DriverFactory;

/* Подключаем формы */
use app\models\forms\ChangeCharNameForm;
use app\models\forms\ChangeCharGenderForm;
use app\models\forms\RemoveKarmaForm;
use app\models\forms\NobleStatusForm;
use app\models\forms\GiftCodeCreateForm;
use app\models\forms\GiftCodeActivateForm;

class ServicesController extends CabinetBaseController
{
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

    /* ----------  ПОДАРОЧНЫЙ КОД  ---------- */
    public function actionGiftCode()
    {
        // Проверяем наличие сервиса в БД
        $service = Services::findOne(['type' => Services::TYPE_GIFT_CODE]);
        if (!$service || $service->status != Services::STATUS_ENABLED) {
            Yii::$app->session->setFlash('error', 'Сервис «Подарочный код» временно недоступен.');
            return $this->redirect(['index']);
        }

        $userId  = Yii::$app->user->id;
        $profile = UserProfiles::findOne(['user_id' => $userId]);

        $createModel   = new GiftCodeCreateForm();
        $activateModel = new GiftCodeActivateForm();

        // Обработка создания кода
        if ($createModel->load(Yii::$app->request->post())) {
            // Разрешенные номиналы
            $allowedNominals = [10, 25, 50, 100, 300, 500, 1000];
            
            if ($createModel->validate() && $createModel->createCode($service, $allowedNominals)) {
                return $this->refresh(); // Перезагрузка, чтобы сбросить POST
            }
        }

        // Обработка активации кода
        if ($activateModel->load(Yii::$app->request->post())) {
            if ($activateModel->validate() && $activateModel->activateCode()) {
                return $this->refresh();
            }
        }

        // Получаем список моих кодов
        $myCodes = \app\models\GiftCode::find()
            ->where(['user_id' => $userId, 'status' => 'active'])
            ->orderBy(['id' => SORT_DESC])
            ->all();

        return $this->render('gift-code', [
            'createModel'   => $createModel,
            'activateModel' => $activateModel,
            'nominals'      => [10, 25, 50, 100, 300, 500, 1000],
            'cost'          => $service->cost, // Комиссия
            'balance'       => $profile->balance ?? 0,
            'myCodes'       => $myCodes,
        ]);
    }

    /* ... Остальные экшены (actionChangeCharName и т.д.) оставляем без изменений ... */
    // (Для сокращения ответа я не дублирую остальные методы, так как они у тебя уже есть рабочие)
    
    public function actionChangeCharName(?int $gs_id = null) { /* Твой старый код */ 
         return $this->redirect(['index']); // заглушка, вставь свой код
    }
    public function actionChangeGender(?int $gs_id = null) { /* Твой старый код */ 
         return $this->redirect(['index']); // заглушка, вставь свой код
    }
    public function actionRemoveKarma(?int $gs_id = null) { /* Твой старый код */ 
         return $this->redirect(['index']); // заглушка, вставь свой код
    }
    public function actionNobleStatus(?int $gs_id = null) { /* Твой старый код */ 
         return $this->redirect(['index']); // заглушка, вставь свой код
    }
}