<?php
namespace app\modules\install\controllers;

use Yii;
use yii\web\Controller;
use yii\helpers\FileHelper;
use app\modules\install\models\{PhpCheckForm, DbForm, AdminForm};

class SetupController extends Controller
{
    public function actionIndex()
    {
        $model = new PhpCheckForm();
        $failed = $model->getFailedCount();

        if (Yii::$app->request->isPost) {
            $accept = Yii::$app->request->post('accept_license');

            if (!$accept) {
                Yii::$app->session->setFlash('error', Yii::t('install', 'Вы должны принять условия лицензии, чтобы продолжить установку.'));
            } else {
                // Ставим метку, что Шаг 1 пройден, и пускаем дальше
                Yii::$app->session->set('install_step1_passed', true);
                return $this->redirect(['step2']);
            }
        }

        return $this->render('step1', [
            'model'  => $model,
            'failed' => $failed
        ]);
    }

    public function actionStep2()
    {
        if (!Yii::$app->session->get('install_step1_passed')) {
            return $this->redirect(['index']);
        }

        $model = new DbForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->writeConfig();
            Yii::$app->session->set('install_step2_passed', true);
            return $this->redirect(['step3']);
        }
        return $this->render('step2', ['model' => $model]);
    }

    public function actionStep3()
    {
        if (!Yii::$app->session->get('install_step2_passed')) {
            return $this->redirect(['index']);
        }

        require_once Yii::getAlias('@app/modules/install/migrations/m000000_000001_run_all_sql_files.php');
        $migration = new \m000000_000001_run_all_sql_files();
        $migration->db = Yii::$app->db;
        ob_start();
        $migration->up();
        $res = ob_get_clean();
        return $this->render('step3', ['res' => $res]);
    }

    public function actionStep4()
    {
        $model = new AdminForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->createAdmin();
            return $this->redirect(['step5']);
        }
        return $this->render('step4', ['model' => $model]);
    }

    public function actionStep5()
    {
        $installPath = Yii::getAlias('@app/modules/install');
        $removed = false;
        $error = null;

        try {
            if (is_dir($installPath)) {
                FileHelper::removeDirectory($installPath);
                $removed = true;
            }
        } catch (\Throwable $e) {
            $error = $e->getMessage();
        }

        return $this->render('step5', ['removed' => $removed, 'error' => $error]);
    }
}