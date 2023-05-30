<?php

/** @var yii\web\View $this */
/** @var \yii\data\ActiveDataProvider $dataProvider */

$this->title = 'My Yii Application';
?>
<div class="site-index">
    <div class="body-content">
        <!-- Section-->
        <section class="">
            <div class="container">
                <?= \yii\widgets\ListView::widget([
                    'dataProvider' => $dataProvider,
                    'layout' => '{summary}<div class="row gx-4 gx-lg-5 row-cols-2 row-cols-md-3 row-cols-xl-4">{items}</div>{pager}',
                    'itemView' => '_product_item',
                    'itemOptions' => [
                        'class' => 'col mb-5 product-item',
                    ],
                    'pager' => [
                        'class' => \yii\bootstrap5\LinkPager::class,
                    ],
                ]) ?>
            </div>
        </section>
    </div>
</div>
