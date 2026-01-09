<?php
/**
 * @var \app\models\Gs[] $servers
 * @var int|null $gs_id
 * @var array $characters
 * @var int|null $char_id
 * @var float $balance
 */
use yii\helpers\Html;
use yii\helpers\Url;

// Определяем текущий экшен, чтобы форма отправлялась на ту же страницу (смена ника, смена пола и т.д.)
$currentRoute = '/' . Yii::$app->controller->route;
?>
<div class="panel panel-default" style="background: rgba(0,0,0,0.3); border: 1px solid #444;">
    <div class="panel-body">
        <form method="get" action="<?= Url::to([$currentRoute]) ?>" class="form-inline" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">

            <div class="form-group" style="margin-right: 15px;">
                <label class="control-label" style="margin-right: 5px; color: #ccc;">Сервер:</label>
                <select name="gs_id" class="form-control" onchange="this.form.submit()" style="background: #222; color: #fff; border: 1px solid #555;">
                    <option value="">— Выберите сервер —</option>
                    <?php foreach ($servers as $s): ?>
                        <option value="<?= (int)$s->id ?>" <?= (int)$gs_id === (int)$s->id ? 'selected' : '' ?>>
                            <?= Html::encode($s->name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group" style="margin-right: 15px;">
                <label class="control-label" style="margin-right: 5px; color: #ccc;">Персонаж:</label>
                <select name="char_id" class="form-control" <?= (!$gs_id || empty($characters)) ? 'disabled' : '' ?> onchange="this.form.submit()" style="background: #222; color: #fff; border: 1px solid #555;">
                    <option value="">— Выберите персонажа —</option>
                    <?php if (!empty($characters)): ?>
                        <?php foreach ($characters as $ch): ?>
                            <option value="<?= (int)$ch['char_id'] ?>" <?= (int)($char_id ?? 0) === (int)$ch['char_id'] ? 'selected' : '' ?>>
                                <?= Html::encode($ch['char_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="form-group">
                <span class="text-muted">Баланс:</span>
                <span style="color: #cda45e; font-weight: bold; font-size: 16px;"><?= (int)$balance ?> Web Aden</span>
            </div>

        </form>
    </div>
</div>