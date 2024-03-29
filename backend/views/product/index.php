<?php

use common\models\Product;
use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var backend\models\search\ProductSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Products';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a('Create Product', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <div class="table-responsive">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                [
                    'attribute' => 'id',
                    'contentOptions' => ['style' => 'width:60px'],
                ],
                [
                    'label' => 'Image',
                    'attribute' => 'image',
                    'content' => static function ($model) {
                        /** @var Product $model */
                        return Html::img($model->getImageUrl(), ['style' => 'width:50px']);
                    },
                ],
                [
                    'attribute' => 'name',
                    'content' => static function ($model) {
                        /** @var Product $model */
                        return StringHelper::truncateWords($model->name, 7);
                    },
                ],
                'price:currency',
                [
                    'attribute' => 'status',
                    'content' => function ($model) {
                        /** @var Product $model */
                        return Html::tag('span', $model->status ? 'Active' : 'Draft',
                            ['class' => 'badge badge-' . ($model->status ? 'success' : 'danger')]);
                    },
                ],
                [
                    'attribute' => 'created_at',
                    'format' => ['datetime'],
                    'contentOptions' => ['style' => 'white-space: nowrap'],
                ],
                [
                    'attribute' => 'updated_at',
                    'format' => ['datetime'],
                    'contentOptions' => ['style' => 'white-space: nowrap'],
                ],
                //'created_by',
                //'updated_by',
                [
                    'class' => ActionColumn::class,
                    'contentOptions' => [
                        'class' => 'td-actions',
                        //  'style' => 'width:90px',
                    ],
                    'urlCreator' => function ($action, Product $model, $key, $index, $column) {
                        return Url::toRoute([$action, 'id' => $model->id]);
                    }
                ],
            ],
        ]) ?>
    </div>
</div>