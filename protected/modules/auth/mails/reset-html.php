<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this \yii\web\View */
/* @var $user \app\models\Users */

$resetLink = Url::to(['/auth/default/reset-password', 'token' => $user->reset_token], true);
?>
<p><?= Yii::t('views', 'Здравствуйте!') ?></p>
<p><?= Yii::t('views', 'Для сброса пароля перейдите по ссылке:') ?></p>
<p><?= Html::a(Html::encode($resetLink), $resetLink) ?></p>
<p><?= Yii::t('views', 'Ссылка действительна {hours} ч.', [
    'hours' => Yii::$app->params['auth.resetTokenExpire'] / 3600,
]) ?></p>