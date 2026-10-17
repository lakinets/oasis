<?php
use app\modules\install\assets\InstallAsset;
use yii\helpers\Html;
use yii\helpers\Url;

InstallAsset::register($this);

// Ссылки для переключения языка с сохранением текущего URL
$ruUrl = Url::current(['lang' => 'ru']);
$enUrl = Url::current(['lang' => 'en']);
?>
<?php $this->beginPage(); ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head(); ?>
    <style>
        .lang-switcher {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 16px;
            font-weight: bold;
            z-index: 1000;
            background: rgba(255,255,255,0.8);
            padding: 5px 15px;
            border-radius: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .lang-switcher a {
            text-decoration: none;
            color: #6c757d;
            margin: 0 5px;
        }
        .lang-switcher a.active {
            color: #0d6efd;
        }
    </style>
</head>
<body class="bg-light">
<?php $this->beginBody(); ?>

<div class="lang-switcher shadow-sm">
    <?= Html::a('RU', $ruUrl, ['class' => Yii::$app->language === 'ru' ? 'active' : '']) ?> |
    <?= Html::a('EN', $enUrl, ['class' => Yii::$app->language === 'en' ? 'active' : '']) ?>
</div>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm rounded-3">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><?= Html::encode($this->title) ?></h4>
                </div>
                <div class="card-body">
                    <?= $content ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->endBody(); ?>
</body>
</html>
<?php $this->endPage(); ?>