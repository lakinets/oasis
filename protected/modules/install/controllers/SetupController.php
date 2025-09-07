<?php
namespace app\modules\install\controllers;

use Yii;
use yii\web\Controller;
use yii\helpers\FileHelper;
use app\modules\install\models\{PhpCheckForm, DbForm, AdminForm};

class SetupController extends Controller
{
    /* ---------- шаг 1 : проверка окружения ---------- */
    public function actionIndex()
    {
        return $this->render('step1', ['model' => new PhpCheckForm()]);
    }

    /* ---------- шаг 2 : подключение к БД ---------- */
    public function actionStep2()
    {
        $model = new DbForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->writeConfig();           // создаёт config/db.php
            return $this->redirect(['step3']);
        }
        return $this->render('step2', ['model' => $model]);
    }

    /* ---------- шаг 3 : миграции (без all_items.sql и oasis.sql) ---------- */
    public function actionStep3()
    {
        require_once Yii::getAlias('@app/modules/install/migrations/m000000_000001_run_all_sql_files.php');

        $migration = new \m000000_000001_run_all_sql_files();
        $migration->db = Yii::$app->db;

        ob_start();
        $migration->up();
        $res = ob_get_clean();

        return $this->render('step3', ['res' => $res]);
    }

    /* ---------- шаг 4 : создаём админа ---------- */
    public function actionStep4()
    {
        $model = new AdminForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->createAdmin();
            return $this->redirect(['step5']);
        }
        return $this->render('step4', ['model' => $model]);
    }

    /* ---------- шаг 5 : финал ---------- */
    public function actionStep5()
    {
        $installPath = Yii::getAlias('@app/protected/modules/install');
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

        return $this->render('step5', [
            'removed' => $removed,
            'error'   => $error,
        ]);
    }
}
