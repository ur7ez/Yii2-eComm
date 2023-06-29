<?php

use common\models\Order;
use yii\bootstrap5\LinkPager;
use yii\bootstrap5\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var backend\models\search\OrderSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Orders';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Order', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'id' => 'orders-table',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pager' => [
            'class' => LinkPager::class,
        ],
        'columns' => [
            [
                'attribute' => 'id',
                'contentOptions' => ['style' => 'width: 80px;'],
            ],
            [
                'attribute' => 'fullname',
                'content' => function ($model) {
                    return $model->firstname . ' ' . $model->lastname;
                }
            ],
            'total_price:currency',
            //'email:email',
            //'transaction_id',
            //'paypal_order_id',
            [
                'attribute' => 'status',
                'filter' => Html::activeDropDownList(
                    $searchModel, 'status', Order::getStatusLabels(), [
                        'class' => 'form-control',
                        'prompt' => 'All'
                    ]
                ),
                'format' => ['orderStatus'],
            ],
            'created_at:datetime',
            //'created_by',
            [
                'class' => ActionColumn::class,
                'template' => '{view} {delete}',
                'urlCreator' => function ($action, Order $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                }
            ],
        ],
    ]); ?>


</div>
