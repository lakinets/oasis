<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var \yii\base\Model $createModel
 * @var \yii\base\Model $activateModel
 * @var array $nominals
 * @var float $cost
 * @var float $balance
 * @var \app\models\GiftCode[] $myCodes
 */

$this->title = 'Подарочный код';
?>

<ul class="nav-mini">
    <li><a href="/cabinet/services" class="active">Назад к услугам</a></li>
    <li><a href="/cabinet/deposit">Пополнение</a></li>
</ul>

<h1 class="orion-table-header"><?= Html::encode($this->title) ?></h1>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div></div>
    <div class="text-end">
        <small class="text-muted">Баланс</small>
        <div class="fs-5 fw-semibold text-warning"><?= (int)$balance ?> Web Aden</div>
    </div>
</div>

<?php if (Yii::$app->session->hasFlash('success')): ?>
    <div class="alert alert-success"><?= Yii::$app->session->getFlash('success') ?></div>
<?php endif; ?>
<?php if (Yii::$app->session->hasFlash('error')): ?>
    <div class="alert alert-danger"><?= Yii::$app->session->getFlash('error') ?></div>
<?php endif; ?>

<div class="row">
    <div class="col-md-6">
        <div class="box p-20">
            <h3>Создать код</h3>
            <?php $form = ActiveForm::begin(['id' => 'create-form']); ?>
            
            <?= $form->field($createModel, 'nominal')->dropDownList(
                array_combine($nominals, $nominals), 
                ['prompt' => '– Выберите номинал –', 'class' => 'form-control']
            ) ?>
            
            <div class="alert alert-info" style="font-size: 0.9em;">
                Комиссия сервиса: <b><?= $cost ?> Web Aden</b><br>
                <hr>
                <small>Вы создаете код, который можно передать другому игроку. С вашего баланса спишется <b>Номинал + Комиссия</b>.</small>
            </div>
            
            <?= Html::submitButton('Создать и оплатить', ['class' => 'btn btn-success w-100']) ?>
            <?php ActiveForm::end(); ?>
        </div>
    </div>

    <div class="col-md-6">
        <div class="box p-20">
            <h3>Активировать код</h3>
            <?php $form = ActiveForm::begin(['id' => 'activate-form']); ?>
            
            <?= $form->field($activateModel, 'code')->textInput([
                'maxlength' => 32, 
                'placeholder' => 'Введите код...',
                'class' => 'form-control'
            ]) ?>
            
            <div class="alert alert-warning" style="font-size: 0.9em;">
                <small>После 5 неудачных попыток ввода возможность активации блокируется на 72 часа.</small>
            </div>
            
            <?= Html::submitButton('Активировать', ['class' => 'btn btn-primary w-100']) ?>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<div class="box p-20 mt-20">
    <h3>Мои активные коды (созданные мной)</h3>
    <?php if (empty($myCodes)): ?>
        <p class="text-muted">У вас нет активных созданных кодов.</p>
    <?php else: ?>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Код</th>
                    <th>Номинал</th>
                    <th>Создан</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($myCodes as $code): ?>
                    <tr>
                        <td style="font-family: monospace; font-size: 1.2em; color: #cda45e;">
                            <?= Html::encode($code->code) ?>
                        </td>
                        <td><?= $code->nominal ?> Web Aden</td>
                        <td><?= Yii::$app->formatter->asDatetime($code->created_at) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>