<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var $model app\modules\backend\models\UserBonuses */
/** @var $bonuses app\modules\backend\models\Bonuses[] */

$this->title = Yii::t('backend', 'Добавить бонус пользователю');
?>
<h1><?= Html::encode($this->title) ?></h1>

<div class="user-bonuses-form">
    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'login')->textInput(['name' => 'login', 'placeholder' => Yii::t('backend', 'Введите логин')]) ?>

    <?= $form->field($model, 'bonus_id')->dropDownList(
        \yii\helpers\ArrayHelper::map($bonuses, 'id', 'title'),
        ['prompt' => Yii::t('backend', 'Выберите бонус')]
    ) ?>

    <?= $form->field($model, 'status')->dropDownList([
        0 => Yii::t('backend', 'Не активирован'),
        1 => Yii::t('backend', 'Активирован'),
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('backend', 'Сохранить'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

