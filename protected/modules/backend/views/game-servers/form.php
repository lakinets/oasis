<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\backend\models\LoginServers;

/** @var yii\web\View $this */
/** @var app\modules\backend\models\Gs $model */
/** @var array $versions */

$this->title = $model->isNewRecord
    ? Yii::t('backend', 'Добавление игрового сервера')
    : Yii::t('backend', 'Редактирование игрового сервера');

$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Игровые сервера'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gs-form">
    <h1><?= Html::encode($this->title) ?></h1>




    <?php $form = ActiveForm::begin([
        'id' => 'gs-form',
        'options' => ['class' => 'form-horizontal'],
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-9\">{input}</div>\n<div class=\"col-lg-9\">{error}</div>",
            'labelOptions' => ['class' => 'col-lg-3 control-label'],
        ],
    ]); ?>

    <legend><?= Yii::t('backend', 'Заполните все поля вашими данными') ?></legend>
    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'ip')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'port')->textInput(['type' => 'number']) ?>
    <?= $form->field($model, 'login_id')->dropDownList(
        ArrayHelper::map(LoginServers::find()->all(), 'id', 'name'),
        ['prompt' => Yii::t('backend', 'Выбрать')]
    ) ?>
    <?= $form->field($model, 'version')->dropDownList($versions ?? [], ['prompt' => '']) ?>
    <?= $form->field($model, 'status')->dropDownList($model->getStatusList()) ?>

    <legend><?= Yii::t('backend', 'Подключение к базе данных') ?></legend>
    <?= $form->field($model, 'db_host')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'db_port')->textInput(['type' => 'number']) ?>
    <?= $form->field($model, 'db_user')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'db_pass')->passwordInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'db_name')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <div class="col-sm-offset-3 col-sm-9">
            <?= Html::submitButton($model->isNewRecord ? Yii::t('backend', 'Создать') : Yii::t('backend', 'Сохранить'), ['class' => 'btn btn-primary']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>