<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var \app\models\Gs[] $servers
 * @var int|null $gs_id
 * @var array $characters
 * @var array $charMap
 * @var \app\models\forms\ChangeCharNameForm $model
 * @var float $cost
 * @var float $balance
 */

$this->title = Yii::t('views', 'Смена имени персонажа');
?>

<ul class="nav-mini">
    <li><a href="/cabinet/tickets"><?= Yii::t('views', 'Поддержка') ?></a></li>
    <li><a href="/cabinet/characters"><?= Yii::t('views', 'Персонажи') ?></a></li>
    <li><a href="/cabinet/shop"><?= Yii::t('views', 'Магазин') ?></a></li>
    <li><a href="/cabinet/bonuses"><?= Yii::t('views', 'Бонусы') ?></a></li>
    <li><a href="/cabinet/security"><?= Yii::t('views', 'Безопасность') ?></a></li>
    <li><a href="/cabinet/messages"><?= Yii::t('views', 'Сообщения') ?></a></li>
    <li><a href="/cabinet/deposit"><?= Yii::t('views', 'Пополнение') ?></a></li>
    <li><a href="/cabinet/transaction-history"><?= Yii::t('views', 'История транзакций') ?></a></li>
    <li><a href="/cabinet/auth-history"><?= Yii::t('views', 'История входов') ?></a></li>
    <li><a href="/cabinet/referals"><?= Yii::t('views', 'Рефералы') ?></a></li>
    <li><a href="/cabinet/services"><?= Yii::t('views', 'Услуги') ?></a></li>
</ul>

<h1 class="orion-table-header"><?= Html::encode($this->title) ?></h1>

<!-- БАЛАНС в шапке -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <div></div>
    <div class="text-end">
        <small class="text-muted"><?= Yii::t('views', 'Баланс') ?></small>
        <div class="fs-5 fw-semibold text-warning"><?= (int)$balance ?> Web Aden</div>
    </div>
</div>

<!-- FLASH-сообщения -->
<?php if (Yii::$app->session->hasFlash('success')): ?>
    <div class="alert alert-success"><?= Yii::$app->session->getFlash('success') ?></div>
<?php endif; ?>
<?php if (Yii::$app->session->hasFlash('error')): ?>
    <div class="alert alert-danger"><?= Yii::$app->session->getFlash('error') ?></div>
<?php endif; ?>

<!-- ОШИБКИ ФОРМЫ -->
<?php if ($model->hasErrors()): ?>
    <div class="alert alert-danger">
        <?php foreach ($model->getErrors() as $attr => $errs): ?>
            <strong><?= $model->getAttributeLabel($attr) ?>:</strong>
            <?= implode('<br>', $errs) ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Кнопки-серверы (как в characters/index) -->
<div style="margin: 10px 0;">
    <?php foreach ($servers as $s): ?>
        <a class="btn btn-sm <?= ($gs_id == $s->id ? 'btn-primary btn-orion' : 'btn-default btn-orion') ?>"
           href="<?= \yii\helpers\Url::to(['change-char-name', 'gs_id' => $s->id]) ?>">
            <?= Html::encode($s->name) ?>
        </a>
    <?php endforeach; ?>
</div>

<?php if (empty($charMap)): ?>
    <div class="alert alert-info"><?= Yii::t('views', 'На выбранном сервере персонажи не найдены.') ?></div>
<?php else: ?>
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <div class="box p-20">
                <?php $form = ActiveForm::begin(['id' => 'change-name-form']); ?>

                    <?= Html::hiddenInput('gs_id', $gs_id) ?>

                    <?= $form->field($model, 'characterId')->dropDownList($charMap, [
                        'prompt' => Yii::t('views', '— Выберите персонажа —'),
                        'class'  => 'form-control'
                    ]) ?>

                    <?= $form->field($model, 'newName')->textInput([
                        'maxlength'   => 16,
                        'placeholder' => Yii::t('views', 'Введите новое имя')
                    ]) ?>

                    <div class="alert alert-info"><?= Yii::t('views', 'Стоимость услуги:') ?> <b><?= $cost ?> Web Aden</b><br>
                        <small><?= Yii::t('views', 'Персонаж должен быть ') ?><b>OFFLINE</b><?= Yii::t('views', ' во время смены ника.') ?></small>
                    </div>

                    <div class="form-group text-center">
                        <?= Html::submitButton(Yii::t('views', 'Сменить имя'), ['class' => 'btn btn-primary btn-lg btn-orion']) ?>
                        <br><br>
                        <?= Html::a(Yii::t('views', '« Назад к услугам'), ['/cabinet/services'], ['class' => 'text-muted']) ?>
                    </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
<?php endif; ?>