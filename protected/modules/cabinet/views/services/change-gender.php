<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var \app\models\Gs[] $servers
 * @var int|null $gs_id
 * @var array $characters
 * @var array $charMap
 * @var \app\models\forms\ChangeCharGenderForm $model
 * @var float $cost
 * @var float $balance
 */

$this->title = 'Смена пола персонажа';
?>

<ul class="nav-mini">
    <li><a href="/cabinet/tickets">Поддержка</a></li>
    <li><a href="/cabinet/characters">Персонажи</a></li>
    <li><a href="/cabinet/shop">Магазин</a></li>
    <li><a href="/cabinet/bonuses">Бонусы</a></li>
    <li><a href="/cabinet/security">Безопасность</a></li>
    <li><a href="/cabinet/messages">Сообщения</a></li>
    <li><a href="/cabinet/deposit">Пополнение</a></li>
    <li><a href="/cabinet/transaction-history">История транзакций</a></li>
    <li><a href="/cabinet/auth-history">История входов</a></li>
    <li><a href="/cabinet/referals">Рефералы</a></li>
    <li><a href="/cabinet/services">Услуги</a></li>
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

<div style="margin: 10px 0;">
    <?php foreach ($servers as $s): ?>
        <a class="btn btn-sm <?= ($gs_id == $s->id ? 'btn-primary btn-orion' : 'btn-default btn-orion') ?>"
           href="<?= \yii\helpers\Url::to(['change-gender', 'gs_id' => $s->id]) ?>">
            <?= Html::encode($s->name) ?>
        </a>
    <?php endforeach; ?>
</div>

<?php if (empty($charMap)): ?>
    <div class="alert alert-info">Нет доступных персонажей для смены пола (возможно, все персонажи – Arteia).</div>
<?php else: ?>
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <div class="box p-20">
                <?php $form = ActiveForm::begin(['id' => 'change-gender-form']); ?>

                    <?= Html::hiddenInput('gs_id', $gs_id) ?>

                    <?= $form->field($model, 'characterId')->dropDownList($charMap, [
                        'prompt' => '– Выберите персонажа –',
                        'class'  => 'form-control'
                    ]) ?>

                    <div class="alert alert-info">
                        Стоимость услуги: <b><?= $cost ?> Web Aden</b><br>
                        <small>Пол будет автоматически изменён на противоположный.<br>
                        Изменение пола <b>недоступно</b> для расы <b>Arteia</b>.</small>
                    </div>

                    <div class="form-group text-center">
                        <?= Html::submitButton('Сменить пол', ['class' => 'btn btn-primary btn-lg btn-orion']) ?>
                        <br><br>
                        <?= Html::a('« Назад к услугам', ['/cabinet/services'], ['class' => 'text-muted']) ?>
                    </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
<?php endif; ?>