<ul class="nav-mini">
    <li><a href="/cabinet/tickets"><?= Yii::t('views', 'Поддержка') ?></a></li>
    <li><a href="/cabinet/characters"><?= Yii::t('views', 'Персонажи') ?></a></li>
    <li><a href="/cabinet/shop"><?= Yii::t('views', 'Магазин') ?></a></li>
    <li><a href="/cabinet/bonuses"><?= Yii::t('views', 'Бонусы') ?></a></li>
    <li><a href="/cabinet/security"><?= Yii::t('views', 'Безопасность') ?></a></li>
    <li><a href="/cabinet/messages"><?= Yii::t('views', 'Сообщения') ?></a></li>
    
    <?php if (!\app\models\Gs::isTopupMode()): ?>
        <li><a href="/cabinet/deposit"><?= Yii::t('views', 'Пополнение') ?></a></li>
    <?php endif; ?>
    
    <li><a href="/cabinet/transaction-history"><?= Yii::t('views', 'История транзакций') ?></a></li>
    <li><a href="/cabinet/auth-history"><?= Yii::t('views', 'История входов') ?></a></li>
    <li><a href="/cabinet/referals"><?= Yii::t('views', 'Рефералы') ?></a></li>
    
    <?php if (!\app\models\Gs::isTopupMode()): ?>
        <li><a href="/cabinet/services"><?= Yii::t('views', 'Услуги') ?></a></li>
    <?php endif; ?>
</ul>
<?php
use yii\grid\GridView;
use yii\helpers\Html;

$this->title = Yii::t('views', 'Мои тикеты');
?>
<h2 class="orion-table-header"><?= Html::encode($this->title) ?></h2>

<?= Html::a(
    Yii::t('views', 'Создать тикет'),
    ['/cabinet/tickets/add'],
    ['class' => 'btn btn-orion mb-3']
) ?>

<div class="table-responsive">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-striped table-bordered'],
        'columns' => [
            'id',
            'title',
            [
                'attribute' => 'status',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->status == 1
                        ? '<span class="badge bg-success">' . Yii::t('views', 'Открыт') . '</span>'
                        : '<span class="badge bg-danger">' . Yii::t('views', 'Закрыт') . '</span>';
                },
            ],
            [
                'attribute' => 'created_at',
                'format' => ['datetime', 'php:d.m.Y H:i'],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::a(
                            Yii::t('views', 'Открыть'),
                            ['/cabinet/tickets/view', 'ticket_id' => $model->id],
                            ['class' => 'btn btn-orion-sm']
                        );
                    },
                ],
            ],
        ],
    ]); ?>
</div>