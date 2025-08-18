<?php
use yii\widgets\ListView;

$this->title = 'Мои бонусы';
?>
<h1>Мои бонусы</h1>

<div class="mb-3">
    <a href="/cabinet/bonuses/bonus-code" class="btn btn-success">Активировать код</a>
</div>

<?= ListView::widget([
    'dataProvider' => $dataProvider,
    'itemView' => '_user_bonus',
    'summary' => '',
]) ?>