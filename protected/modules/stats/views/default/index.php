<?php
/**
 * @var \app\models\Gs[] $gs_list
 * @var \app\models\Gs|null $current
 * @var bool $serverDown
 * @var int $online
 * @var array $totalVars
 * @var array $castles
 * @var array $clans
 */

use yii\helpers\Html;
use yii\helpers\Url;
?>

<h1>Статистика</h1>

<!-- Кнопки серверов -->
<div class="stats-server-buttons" style="margin-bottom:16px;">
    <?php if (!empty($gs_list)): ?>
        <?php foreach ($gs_list as $gs): ?>
            <?php
                $isActive = $current && (int)$current->id === (int)$gs->id;
                $url = Url::to(['index', 'gs_id' => $gs->id]);
            ?>
            <a href="<?= Html::encode($url) ?>"
               style="display:inline-block;margin:4px 6px;padding:8px 12px;border-radius:8px;
                      text-decoration:none; border:1px solid #ccc; <?= $isActive ? 'background:#eef;' : 'background:#fafafa;' ?>">
                <?= Html::encode($gs->name . ' ') ?>
            </a>
        <?php endforeach; ?>
    <?php else: ?>
        <em>Нет доступных серверов</em>
    <?php endif; ?>
</div>

<?php if ($serverDown || !$current): ?>
    <p><strong>Нет данных от сервера.</strong></p>
    <?php return; ?>
<?php endif; ?>

<hr>

<!-- Блоки статистики -->
<div style="display:grid; grid-template-columns: 1fr; gap:24px;">

    <section>
        <h2 style="margin:0 0 8px;">Общая статистика</h2>
        <?php
        // используем существующий макет @app/views/stats/total.php
        echo $this->renderFile(
            Yii::getAlias('@app/views/stats/total.php'),
            $totalVars
        );
        ?>
    </section>

    <section>
        <h2 style="margin:0 0 8px;">Замки</h2>
        <?php
        echo $this->renderFile(
            Yii::getAlias('@app/views/stats/castles.php'),
            ['castles' => $castles]
        );
        ?>
    </section>

    <section>
        <h2 style="margin:0 0 8px;">Кланы (топ)</h2>
        <?php
        echo $this->renderFile(
            Yii::getAlias('@app/views/stats/clans.php'),
            ['clans' => $clans]
        );
        ?>
    </section>
</div>
