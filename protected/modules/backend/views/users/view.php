<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $user app\modules\backend\models\Users */

$this->title = 'Пользователь: ' . Html::encode($user->login);
?>

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title"><?= $this->title ?></h3>
    </div>
    <div class="box-body">
        <table class="table table-striped">
            <tr>
                <th>ID</th>
                <td><?= $user->user_id ?></td>
            </tr>
            <tr>
                <th>Логин</th>
                <td><?= Html::encode($user->login) ?></td>
            </tr>
            <tr>
                <th>Email</th>
                <td><?= Html::encode($user->email) ?></td>
            </tr>
            <tr>
                <th>Роль</th>
                <td><?= $user->getRoleLabel() ?></td>
            </tr>
            <tr>
                <th>Активен</th>
                <td><?= $user->activated ? 'Да' : 'Нет' ?></td>
            </tr>
        </table>

        <div class="btn-group">
            <?= Html::a('Редактировать', ['/backend/users/edit-data', 'user_id' => $user->user_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Назад', ['/backend/users'], ['class' => 'btn btn-default']) ?>
        </div>
    </div>
</div>