<?php
/** @var \common\models\Order $order */
/** @var \common\models\OrderAddress $orderAddress */
/** @var array $cartItems */
/** @var int $productQuantity */
/** @var float $totalPrice */

use yii\bootstrap5\ActiveForm;
?>

<?php $form = ActiveForm::begin([
    'id' => 'checkout-form',
]); ?>
<div class="row">
    <div class="col">
        <div class="card mb-3">
            <div class="card-header">
                <h5><?= Yii::t('app', 'Account Information') ?></h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($order, 'firstname')->textInput(['autofocus' => true]) ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($order, 'lastname')->textInput(['autofocus' => true]) ?>
                    </div>
                </div>
                <?= $form->field($order, 'email')->textInput(['autofocus' => true]) ?>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h5><?= Yii::t('app', 'Order address information') ?></h5>
            </div>
            <div class="card-body">
                <?= $form->field($orderAddress, 'address')->textInput(['autofocus' => true]) ?>
                <?= $form->field($orderAddress, 'city')->textInput(['autofocus' => true]) ?>
                <?= $form->field($orderAddress, 'state')->textInput(['autofocus' => true]) ?>
                <?= $form->field($orderAddress, 'country')->textInput(['autofocus' => true]) ?>
                <?= $form->field($orderAddress, 'zipcode')->textInput(['autofocus' => true]) ?>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card">
            <div class="card-header">
                <h5><?= Yii::t('app', 'Order Summary') ?></h5>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <thead>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Quantity</th>
                        <th>Price</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($cartItems as $item): ?>
                        <tr>
                            <td>
                                <img src="<?= \common\models\Product::formatImageUrl($item['image']) ?>"
                                     style="width: 50px;"
                                     alt="<?= $item['name'] ?>">
                            </td>
                            <td><?= $item['name'] ?></td>
                            <td><?= $item['quantity'] ?></td>
                            <td><?= Yii::$app->formatter->asCurrency($item['total_price']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <table class="table table-striped-columns table-group-divider">
                    <tr>
                        <td>Total Items</td>
                        <td class="text-end"><?= $productQuantity ?></td>
                    </tr>
                    <tr>
                        <td>Total Price</td>
                        <td class="text-end"><?= Yii::$app->formatter->asCurrency($totalPrice) ?></td>
                    </tr>
                </table>
                <p class="mt-3 text-end">
                    <button class="btn btn-secondary"><?= Yii::t('app', 'Checkout') ?></button>
                </p>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>