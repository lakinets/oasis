<?php
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use app\modules\backend\models\Pages;

/* @var $this yii\web\View */
/* @var $model app\modules\backend\models\Pages */
/* @var $translations app\models\PageTranslation[] */
/* @var $languages array */

// 1. Подключаем CSS редактора
$this->registerCssFile('https://cdn.jsdelivr.net/npm/trumbowyg@2.27.0/dist/ui/trumbowyg.min.css');

// 2. Подключаем JS редактора и русский язык (зависит от jQuery, который уже есть в Yii)
$this->registerJsFile('https://cdn.jsdelivr.net/npm/trumbowyg@2.27.0/dist/trumbowyg.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('https://cdn.jsdelivr.net/npm/trumbowyg@2.27.0/dist/langs/ru.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);

// 3. Инициализируем редактор для всех полей с классом .wysiwyg-editor
$this->registerJs(<<<JS
    $(document).ready(function(){
        $('.wysiwyg-editor').each(function(){
            $(this).trumbowyg({
                lang: 'ru',
                btns: [
                    ['viewHTML'],
                    ['undo', 'redo'],
                    ['formatting'],
                    ['strong', 'em', 'del'],
                    ['superscript', 'subscript'],
                    ['link'],
                    ['insertImage'],
                    ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
                    ['unorderedList', 'orderedList'],
                    ['horizontalRule'],
                    ['removeformat'],
                    ['fullscreen']
                ]
            });
        });
    });
JS);
?>

<div class="pages-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'page')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'status')->dropDownList([
                Pages::STATUS_ON  => Yii::t('backend', 'Активна'),
                Pages::STATUS_OFF => Yii::t('backend', 'Не активна'),
            ]) ?>
        </div>
    </div>

    <hr>
    <h4 class="mb-3"><?= Yii::t('backend', 'Переводы') ?></h4>

    <?php if (empty($languages)): ?>
        <div class="alert alert-warning">
            <?= Yii::t('backend', 'Языки не настроены в params.languages') ?>
        </div>
    <?php else: ?>

        <?php $sourceLang = Yii::$app->sourceLanguage; ?>

        <ul class="nav nav-tabs mb-3" role="tablist">
            <?php foreach ($languages as $code => $name): ?>
                <li class="nav-item" role="presentation">
                    <button class="nav-link <?= $code === $sourceLang ? 'active' : '' ?>"
                            id="tab-btn-<?= $code ?>"
                            data-bs-toggle="tab"
                            data-bs-target="#lang-<?= $code ?>"
                            type="button" role="tab"
                            aria-controls="lang-<?= $code ?>"
                            aria-selected="<?= $code === $sourceLang ? 'true' : 'false' ?>">
                        <?= Html::encode($name) ?>
                    </button>
                </li>
            <?php endforeach; ?>
        </ul>

        <div class="tab-content">
            <?php foreach ($languages as $code => $name): ?>
                <div class="tab-pane fade <?= $code === $sourceLang ? 'show active' : '' ?>"
                     id="lang-<?= $code ?>"
                     role="tabpanel"
                     aria-labelledby="tab-btn-<?= $code ?>">

                    <?= $form->field($translations[$code], "[{$code}]title")
                        ->textInput(['maxlength' => true])
                        ->label(Yii::t('backend', 'Заголовок')) ?>

                    <?= $form->field($translations[$code], "[{$code}]text")
                        ->textarea(['rows' => 15, 'class' => 'form-control wysiwyg-editor'])
                        ->label(Yii::t('backend', 'Содержимое')) ?>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <?= $form->field($translations[$code], "[{$code}]seo_title")
                                ->textInput(['maxlength' => true])
                                ->label('SEO title') ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($translations[$code], "[{$code}]seo_keywords")
                                ->textInput(['maxlength' => true])
                                ->label('SEO keywords') ?>
                        </div>
                    </div>

                    <?= $form->field($translations[$code], "[{$code}]seo_description")
                        ->textInput(['maxlength' => true])
                        ->label('SEO description') ?>

                    <?php if ($code !== $sourceLang): ?>
                        <div class="alert alert-info mt-3">
                            <?= Yii::t('backend', 'Если перевод не заполнен — на этом языке будет показан текст с языка «{source}».', [
                                'source' => $languages[$sourceLang] ?? $sourceLang,
                            ]) ?>
                        </div>
                    <?php endif; ?>

                </div>
            <?php endforeach; ?>
        </div>

    <?php endif; ?>

    <hr>
    <div class="form-group mt-3">
        <?= Html::submitButton(
            $model->isNewRecord
                ? Yii::t('backend', 'Добавить')
                : Yii::t('backend', 'Сохранить'),
            ['class' => 'btn btn-primary']
        ) ?>
        <?= Html::a(Yii::t('backend', 'Отмена'), ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>