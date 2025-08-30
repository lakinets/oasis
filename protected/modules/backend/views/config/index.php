<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $groups app\modules\backend\models\ConfigGroup[] */
?>

<h1><?= Yii::t('backend', 'Настройки') ?></h1>

<!-- Меню-якоря -->
<nav class="nav-anchor mb-4">
<?php foreach ($groups as $group): ?>
    <?= Html::a(
        Html::encode($group->name),
        '#group-' . $group->id,
        ['class' => 'btn btn-sm btn-outline-primary me-1', 'data-scroll' => '']
    ) ?>
<?php endforeach; ?>
</nav>

<?php $form = ActiveForm::begin(['id' => 'config-form']); ?>

<?php foreach ($groups as $group): ?>
    <section id="group-<?= $group->id ?>" class="config-group mb-5">
        <h4 class="border-bottom pb-1 mb-3"><?= Html::encode($group->name) ?></h4>

        <?php foreach ($group->configs as $config): ?>
            <div class="mb-3">
                <label class="form-label fw-bold"><?= Html::encode($config->label) ?></label>
                <?= $config->getField() ?>
            </div>
        <?php endforeach; ?>

        <!-- Дублирующая кнопка «Сохранить» под каждой группой -->
        <div class="mt-3">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary', 'form' => 'config-form']) ?>
        </div>
    </section>
<?php endforeach; ?>

<?php ActiveForm::end(); ?>

<!-- Кнопка «Начало» -->
<button id="scroll-to-top" class="btn btn-primary rounded-circle shadow-lg"
        style="position:fixed; bottom:20px; right:20px; z-index:9999; display:none;"
        title="В начало">
    <i class="fas fa-arrow-up"></i>
</button>

<?php
// JS и CSS
$this->registerJs(<<<JS
const btn = document.getElementById('scroll-to-top');
window.addEventListener('scroll', () => {
    btn.style.display = window.scrollY > 200 ? 'block' : 'none';
});
btn.addEventListener('click', () => {
    window.scrollTo({top: 0, behavior: 'smooth'});
});
// плавная прокрутка к якорям
document.querySelectorAll('[data-scroll]').forEach(a=>{
    a.addEventListener('click', e=>{
        e.preventDefault();
        const target=document.querySelector(a.getAttribute('href'));
        if(target) target.scrollIntoView({behavior:'smooth', block:'start'});
    });
});
JS);

$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css ');
?>
<style>
.nav-anchor .btn { margin-bottom: 0.25rem; }
.config-group { scroll-margin-top: 70px; }
</style>