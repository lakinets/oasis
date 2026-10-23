<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\backend\models\News */

$this->title = $model->isNewRecord ? Yii::t('backend', 'Добавить новость') : Yii::t('backend', 'Редактировать новость');

// 1. Подключаем CSS редактора
$this->registerCssFile('https://cdn.jsdelivr.net/npm/trumbowyg@2.27.0/dist/ui/trumbowyg.min.css');

// 2. Подключаем JS редактора, русский язык и плагин загрузки файлов
$this->registerJsFile('https://cdn.jsdelivr.net/npm/trumbowyg@2.27.0/dist/trumbowyg.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('https://cdn.jsdelivr.net/npm/trumbowyg@2.27.0/dist/langs/ru.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('https://cdn.jsdelivr.net/npm/trumbowyg@2.27.0/dist/plugins/upload/trumbowyg.upload.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);

// 3. Генерируем URL для загрузки и CSRF токен
$uploadUrl = Url::to(['upload-image']);
$csrfToken = Yii::$app->request->getCsrfToken();

// 4. CSS для замены иконки облачка на картинку
$this->registerCss("
    .trumbowyg-upload-button .trumbowyg-icons { display: none !important; }
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
                    ['upload'], 
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

<div class="news-form">
    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'status')->checkbox() ?>

    <hr>
    <h4 class="mb-3"><?= Yii::t('backend', 'Переводы') ?></h4>

    <ul class="nav nav-tabs mb-3" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="tab-btn-ru" data-bs-toggle="tab" data-bs-target="#lang-ru" type="button" role="tab" aria-controls="lang-ru" aria-selected="true">
                Русский (RU)
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-btn-en" data-bs-toggle="tab" data-bs-target="#lang-en" type="button" role="tab" aria-controls="lang-en" aria-selected="false">
                English (EN)
            </button>
        </li>
    </ul>

    <div class="tab-content">
        <!-- Вкладка RU -->
        <div class="tab-pane fade show active" id="lang-ru" role="tabpanel" aria-labelledby="tab-btn-ru">
            <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'description')->textarea(['rows' => 5]) ?>
            <?= $form->field($model, 'text')->textarea(['rows' => 15, 'class' => 'form-control wysiwyg-editor']) ?>
        </div>

        <!-- Вкладка EN -->
        <div class="tab-pane fade" id="lang-en" role="tabpanel" aria-labelledby="tab-btn-en">
            <?= $form->field($model, 'title_en')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'description_en')->textarea(['rows' => 5]) ?>
            <?= $form->field($model, 'text_en')->textarea(['rows' => 15, 'class' => 'form-control wysiwyg-editor']) ?>
        </div>
    </div>

    <hr>
    <div class="form-group mt-3">
        <?= Html::submitButton(Yii::t('backend', 'Сохранить'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>