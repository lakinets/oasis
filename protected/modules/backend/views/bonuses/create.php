<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var $model app\modules\backend\models\UserBonuses */
/** @var $bonuses app\modules\backend\models\Bonuses[] */

$this->title = 'Добавить бонус пользователю';
?>
<h1><?= Html::encode($this->title) ?></h1>

<div class="user-bonuses-form">
    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'login')->textInput(['name' => 'login', 'placeholder' => 'Введите логин']) ?>

    <?= $form->field($model, 'bonus_id')->dropDownList(
        \yii\helpers\ArrayHelper::map($bonuses, 'id', 'title'),
        ['prompt' => 'Выберите бонус']
    ) ?>

    <?= $form->field($model, 'status')->dropDownList([
        0 => 'Не активирован',
        1 => 'Активирован',
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
