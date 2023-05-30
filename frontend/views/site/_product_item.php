<?php
/** @var \common\models\Product $model */
?>
<div class="card h-100">
    <!-- Product image-->
    <img class="card-img-top" src="<?= $model->getImageUrl() ?>" alt="..." />
    <!-- Product details-->
    <div class="card-body">
        <h4 class="card-title">
            <a href="#"><?= $model->name ?></a>
        </h4>
        <h5><?= Yii::$app->formatter->asCurrency($model->price) ?></h5>
        <div class="card-text">
            <?= $model->getShortDescription() ?>
        </div>
    </div>
    <!-- Product actions-->
    <div class="card-footer">
        <div class="text-end">
            <a href="<?= \yii\helpers\Url::to(['/cart/add']) ?>" class="btn btn-primary mt-auto btn-add-to-cart">
                Add to Cart
            </a>
        </div>
    </div>
</div>