<?php
/** @var \common\models\Order $order */
/** @var \common\models\OrderAddress $orderAddress */
/** @var \common\models\CartItem[] $cartItems */
/** @var int $productQuantity */
/** @var float $totalPrice */

use yii\bootstrap5\ActiveForm;
?>
<script src="https://www.paypal.com/sdk/js?client-id=AU0al8Aj960qjY5gpCCnoof3Jf7eLAM5CIoirkGrMxeYiuv5BQl_tvf93MUTN76Vxsxx1JD01dwvBuac&currency=USD"></script>
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

                <div id="paypal-button-container"></div>
<!--                <p class="mt-3 text-end">-->
<!--                    <button class="btn btn-secondary">Checkout</button>-->
<!--                </p>-->
            </div>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>
<script>
    paypal.Buttons(
        // {
        //     // Order is created on the server and the order id is returned
        //     createOrder() {
        //         return fetch("/my-server/create-paypal-order", {
        //             method: "POST",
        //             headers: {
        //                 "Content-Type": "application/json",
        //             },
        //             // use the "body" param to optionally pass additional order information
        //             // like product skus and quantities
        //             body: JSON.stringify({
        //                 cart: [
        //                     {
        //                         sku: "YOUR_PRODUCT_STOCK_KEEPING_UNIT",
        //                         quantity: "YOUR_PRODUCT_QUANTITY",
        //                     },
        //                 ],
        //             }),
        //         })
        //             .then((response) => response.json())
        //             .then((order) => order.id);
        //     },
            // Finalize the transaction on the server after payer approval
        //     onApprove(data) {
        //         return fetch("/my-server/capture-paypal-order", {
        //             method: "POST",
        //             headers: {
        //                 "Content-Type": "application/json",
        //             },
        //             body: JSON.stringify({
        //                 orderID: data.orderID
        //             })
        //         })
        //             .then((response) => response.json())
        //             .then((orderData) => {
        //                 // Successful capture! For dev/demo purposes:
        //                 console.log('Capture result', orderData, JSON.stringify(orderData, null, 2));
        //                 const transaction = orderData.purchase_units[0].payments.captures[0];
        //                 alert(`Transaction ${transaction.status}: ${transaction.id}\n
        //             \nSee console for all available details`);
        //                 // When ready to go live, remove the alert and show a success message within this page. For example:
        //                 // const element = document.getElementById('paypal-button-container');
        //                 // element.innerHTML = '<h3>Thank you for your payment!</h3>';
        //                 // Or go to another URL:  window.location.href = 'thank_you.html';
        //             });
        //     }
        // }
    ).render('#paypal-button-container');
</script>