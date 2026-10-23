<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap5\ActiveForm;
use app\modules\backend\models\Pages;

/* @var $this yii\web\View */
/* @var $model app\modules\backend\models\Pages */
/* @var $translations app\models\PageTranslation[] */
/* @var $languages array */

// 1. Подключаем CSS редактора
$this->registerCssFile('https://cdn.jsdelivr.net/npm/trumbowyg@2.27.0/dist/ui/trumbowyg.min.css');

// 2. Подключаем JS редактора, русский язык и плагин загрузки файлов
$this->registerJsFile('https://cdn.jsdelivr.net/npm/trumbowyg@2.27.0/dist/trumbowyg.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('https://cdn.jsdelivr.net/npm/trumbowyg@2.27.0/dist/langs/ru.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('https://cdn.jsdelivr.net/npm/trumbowyg@2.27.0/dist/plugins/upload/trumbowyg.upload.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);

// 3. Генерируем URL для загрузки
$uploadUrl = Url::to(['upload-image']);
$csrfToken = Yii::$app->request->getCsrfToken();

// 4. CSS-трюк: прячем облачко и показываем иконку картинки
$this->registerCss("
    /* Скрываем стандартную иконку загрузки (облачко) */
    .trumbowyg-upload-button .trumbowyg-icons { display: none !important; }
    
    /* Рисуем иконку картинки на месте кнопки */
    .trumbowyg-upload-button::before {
        content: '';
        display: inline-block;
        width: 18px;
        height: 18px;
        background-color: currentColor;
        -webkit-mask-image: url(\"data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z'/%3E%3C/svg%3E\");
        mask-image: url(\"data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z'/%3E%3C/svg%3E\");
        -webkit-mask-size: contain;
        mask-size: contain;
        -webkit-mask-repeat: no-repeat;
        mask-repeat: no-repeat;
        -webkit-mask-position: center;
        mask-position: center;
        vertical-align: middle;
    }
");

// 5. Инициализируем редактор
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
                    ['upload'], // Кнопка выбора файла с жесткого диска
                    ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
                    ['unorderedList', 'orderedList'],
                    ['horizontalRule'],
                    ['removeformat'],
                    ['fullscreen']
                ],
                plugins: {
                    upload: {
                        serverPath: '{$uploadUrl}',
                        fileFieldName: 'file',
                        urlPropertyName: 'file',
                        statusPropertyName: 'success',
                        headers: {
                            'X-CSRF-Token': '{$csrfToken}'
                        }
                    }
                }
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