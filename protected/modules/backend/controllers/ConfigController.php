<?php
namespace app\modules\backend\controllers;

use Yii;
use app\modules\backend\models\ConfigGroup;
use app\modules\backend\models\Config;

class ConfigController extends BackendController
{
    public function actionIndex()
    {
        $groups = ConfigGroup::findOpened()
            ->orderBy(['order' => SORT_ASC])
            ->with('configs')
            ->all();

        if (Yii::$app->request->isPost) {
            $postData = Yii::$app->request->post('Config', []);
            if (!empty($postData)) {
                $newAdminUrl = null;
                $reserved = ['cabinet', 'login', 'logout', 'register', 'stats', 'news', 'gallery', 'page', 'site', 'install', 'backend'];

                foreach ($postData as $id => $row) {
                    $config = Config::findOne($id);
                    if ($config !== null && isset($row['value'])) {
                        $val = $row['value'];
                        
                        // Если это чекбоксы (массив) — сохраняем как JSON
                        if (is_array($val)) {
                            $val = json_encode($val);
                        }

                        // Спец-обработка для URL админки
                        if ($config->param === 'admin.url') {
                            $val = trim($val);
                            if (!preg_match('/^[a-zA-Z0-9_-]+$/', $val)) {
                                Yii::$app->session->setFlash('error', 'URL админ-панели может содержать только буквы, цифры, дефис и подчеркивание.');
                                return $this->refresh();
                            }
                            if (in_array(strtolower($val), $reserved, true) && $val !== 'backend') {
                                Yii::$app->session->setFlash('error', 'Этот URL зарезервирован системой. Выберите другой.');
                                return $this->refresh();
                            }
                            if ($val !== $config->value) {
                                $newAdminUrl = $val;
                            }
                        }

                        $config->value = $val;
                        $config->save(false, ['value']);
                    }
                }
                
                Yii::$app->session->setFlash('success', Yii::t('backend', 'Настройки успешно сохранены.'));

                // Если админ сменил URL, перекидываем его на новый адрес
                if ($newAdminUrl !== null) {
                    Yii::$app->session->setFlash('info', 'URL админ-панели изменен. Новый адрес: /' . $newAdminUrl);
                    return $this->redirect('/' . $newAdminUrl . '/config');
                }
            }
            return $this->refresh();
        }

        return $this->render('index', [
            'groups' => $groups,
        ]);
    }
}