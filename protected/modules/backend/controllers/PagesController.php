<?php
namespace app\modules\backend\controllers;

use Yii;
use yii\base\Model;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;

use app\modules\backend\models\Pages;
use app\modules\backend\models\PagesSearch;
use app\models\PageTranslation;

/**
 * Управление статическими страницами.
 * Наследует BackendController => вход только для admin.
 */
class PagesController extends BackendController
{
    /* ---------- 1. Список страниц ---------- */
    public function actionIndex()
    {
        $searchModel  = new PagesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel'  => $searchModel,
        ]);
    }

    /* ---------- 2. Добавление / редактирование ---------- */
    public function actionForm($id = null)
    {
        $model = $id ? $this->findModel($id) : new Pages();

        $languages = Yii::$app->params['languages'] ?? ['ru' => 'Русский', 'en' => 'English'];

        // Готовим translation-модели для каждого языка
        $translations = [];
        foreach (array_keys($languages) as $code) {
            if ($model->isNewRecord) {
                $t = new PageTranslation();
            } else {
                $t = PageTranslation::findOne([
                    'page_id'  => $model->id,
                    'language' => $code,
                ]) ?: new PageTranslation();
            }
            $t->language = $code;
            $translations[$code] = $t;
        }

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());

            // ✅ КЛЮЧЕВАЯ СТРОКА — Yii сам разложит PageTranslation[ru][...] и [en][...] по моделям
            Model::loadMultiple($translations, Yii::$app->request->post());

            if ($model->validate() && Model::validateMultiple($translations)) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    $now = date('Y-m-d H:i:s');
                    if ($model->isNewRecord) {
                        $model->created_at = $now;
                    } else {
                        $model->updated_at = $now;
                    }

                    // Fallback: колонки pages.* = sourceLanguage (для совместимости)
                    $sourceLang = Yii::$app->sourceLanguage;
                    if (isset($translations[$sourceLang])) {
                        $src = $translations[$sourceLang];
                        $model->title           = $src->title;
                        $model->text            = $src->text;
                        $model->seo_title       = $src->seo_title;
                        $model->seo_keywords    = $src->seo_keywords;
                        $model->seo_description = $src->seo_description;
                    }

                    if (!$model->save(false)) {
                        throw new \Exception('Не удалось сохранить страницу: ' .
                            json_encode($model->getErrors(), JSON_UNESCAPED_UNICODE));
                    }

                    foreach ($translations as $code => $t) {
                        $t->page_id  = $model->id;
                        $t->language = $code;
                        if (!$t->save(false)) {
                            throw new \Exception("Не удалось сохранить перевод '{$code}': " .
                                json_encode($t->getErrors(), JSON_UNESCAPED_UNICODE));
                        }
                    }

                    $transaction->commit();

                    Yii::$app->session->setFlash('success', $id
                        ? Yii::t('backend', 'Страница сохранена.')
                        : Yii::t('backend', 'Страница добавлена.')
                    );
                    return $this->redirect(['index']);
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', $e->getMessage());
                }
            } else {
                // Собираем ошибки
                $errors = $model->getErrors();
                foreach ($translations as $code => $t) {
                    if ($t->hasErrors()) {
                        $errors["translation_{$code}"] = $t->getErrors();
                    }
                }
                $flat = [];
                array_walk_recursive($errors, function ($v) use (&$flat) {
                    if (is_string($v)) $flat[] = $v;
                });
                Yii::$app->session->setFlash('error', implode('<br>', $flat));
            }
        }

        return $this->render('form', [
            'model'        => $model,
            'translations' => $translations,
            'languages'    => $languages,
        ]);
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
        $model = Pages::findOne($id);
        if (!$model) {
            Yii::$app->session->setFlash('error', Yii::t('backend', 'Страница не найдена.'));
            return $this->redirect(['index']);
        }

        $model->status = Pages::STATUS_DELETED;
        if ($model->save(false, ['status'])) {
            Yii::$app->session->setFlash('success', Yii::t('backend', 'Страница удалена.'));
        } else {
            Yii::$app->session->setFlash('error', Yii::t('backend', 'Не удалось удалить страницу.'));
        }
        return $this->redirect(['index']);
    }

    /* ---------- 5. Поиск модели ---------- */
    protected function findModel($id)
    {
        if (($model = Pages::find()
                ->andWhere(['status' => [Pages::STATUS_ON, Pages::STATUS_OFF]])
                ->andWhere(['id' => $id])
                ->one()) === null) {
            throw new NotFoundHttpException(Yii::t('backend', 'Страница не найдена.'));
        }
        return $model;
    }
}