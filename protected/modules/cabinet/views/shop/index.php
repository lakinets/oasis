<?php
$this->title = 'Магазин';
?>
<h1>Категории магазина</h1>

<div class="row">
    <?php foreach ($categories as $category): ?>
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><?= Html::encode($category->name) ?></h5>
                    <a href="/cabinet/shop/category?category_link=<?= $category->link ?>" class="btn btn-primary">Перейти</a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>