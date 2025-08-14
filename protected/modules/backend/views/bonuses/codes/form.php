<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\backend\models\Bonuses;

/** @var $this yii\web\View */
/** @var $model app\modules\backend\models\BonusCodes */

$this->title = $model->isNewRecord ? 'Создать код' : 'Редактировать код';
$this->params['breadcrumbs'][] = ['label'=>'Коды','url'=>['codes']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bonus-codes-form">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model,'code')->textInput(['maxlength'=>128]) ?>
    <?= $form->field($model,'bonus_id')->dropDownList(\yii\helpers\ArrayHelper::map(Bonuses::find()->all(),'id','title'), ['prompt'=>'Выбрать']) ?>
    <?= $form->field($model,'limit')->input('number') ?>
    <?= $form->field($model,'status')->dropDownList([1=>'Активирован',0=>'Выключен']) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class'=>'btn btn-primary']) ?>
        <?= Html::a('Отмена',['codes'],['class'=>'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<script>
    // Генерация кода через AJAX
    (function(){
        document.addEventListener('click', function(e){
            if(e.target && e.target.classList.contains('js-generate-code')){
                e.preventDefault();
                fetch('<?= \yii\helpers\Url::to(['generate-code']) ?>').then(r=>r.text()).then(t=>{
                    document.querySelector('#bonuscodes-code').value = t;
                });
            }
        });
    })();
</script>
