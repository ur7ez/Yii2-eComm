<?php
/** @var \common\models\Order $order */
/** @var \common\models\OrderAddress $orderAddress */
/** @var \common\models\CartItem[] $cartItems */
/** @var int $productQuantity */
/** @var float $totalPrice */

use yii\bootstrap5\ActiveForm;
?>
<?php $form = ActiveForm::begin([
    'action' => [''],
]); ?>

<div class="row">
    <div class="col">
        <div class="card mb-3">
            <div class="card-header">
                <h5>Account information</h5>
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
                <h5>Order address information</h5>
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
                <h5>Order Summary</h5>
            </div>
            <div class="card-body">
                <table class="table table-striped-columns table-group-divider">
                    <tr>
                        <td colspan="2"><?= $productQuantity ?> Products</td>
                    </tr>
                    <tr>
                        <td>Total Price</td>
                        <td class="text-end"><?= Yii::$app->formatter->asCurrency($totalPrice) ?></td>
                    </tr>
                </table>
                <p class="mt-3 text-end">
                    <button class="btn btn-secondary">Checkout</button>
                </p>
            </div>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>