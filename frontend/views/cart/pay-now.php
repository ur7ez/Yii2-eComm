<?php

use yii\helpers\Url;

/** @var \common\models\Order $order */

$orderAddress = $order->orderAddress;
?>
<script src="https://www.paypal.com/sdk/js?client-id=<?= param('paypalClientId') ?>&disable-funding=credit&currency=USD"></script>

<h3>Order #<?= $order->id ?> summary:</h3>
<br>
<div class="row">
    <div class="col">
        <h5><?= Yii::t('app', 'Account Information') ?></h5>
        <table class="table">
            <tr>
                <th><?= Yii::t('app', 'Firstname') ?></th>
                <td><?= $order->firstname ?></td>
            </tr>
            <tr>
                <th><?= Yii::t('app', 'Lastname') ?></th>
                <td><?= $order->lastname ?></td>
            </tr>
            <tr>
                <th><?= Yii::t('app', 'Email') ?></th>
                <td><?= $order->email ?></td>
            </tr>
        </table>
        <h5><?= Yii::t('app', 'Address Information') ?></h5>
        <table class="table">
            <tr>
                <th><?= Yii::t('app', 'Address') ?></th>
                <td><?= $orderAddress->address ?></td>
            </tr>
            <tr>
                <th><?= Yii::t('app', 'City') ?></th>
                <td><?= $orderAddress->city ?></td>
            </tr>
            <tr>
                <th><?= Yii::t('app', 'State') ?></th>
                <td><?= $orderAddress->state ?></td>
            </tr>
            <tr>
                <th><?= Yii::t('app', 'Country') ?></th>
                <td><?= $orderAddress->country ?></td>
            </tr>
            <tr>
                <th><?= Yii::t('app', 'ZipCode') ?></th>
                <td><?= $orderAddress->zipcode ?></td>
            </tr>
        </table>
    </div>
    <div class="col">
        <h5><?= Yii::t('app', 'Products') ?></h5>
        <table class="table table-sm">
            <thead>
            <tr>
                <th><?= Yii::t('app', 'Image') ?></th>
                <th><?= Yii::t('app', 'Name') ?></th>
                <th><?= Yii::t('app', 'Quantity') ?></th>
                <th><?= Yii::t('app', 'Price') ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($order->orderItems as $item): ?>
                <tr>
                    <td>
                        <img src="<?= $item->product->getImageUrl() ?>" alt=""
                             style="width: 50px;">
                    </td>
                    <td><?= $item->product_name ?></td>
                    <td><?= $item->quantity ?></td>
                    <td><?= \Yii::$app->formatter->asCurrency($item->quantity * $item->unit_price) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <hr>
        <table class="table">
            <tr>
                <th><?= Yii::t('app', 'Total Items') ?></th>
                <td><?= $order->getItemsQuantity() ?></td>
            </tr>
            <tr>
                <th><?= Yii::t('app', 'Total Price') ?></th>
                <td><?= \Yii::$app->formatter->asCurrency($order->total_price) ?></td>
            </tr>
        </table>
        <div id='paypal-button-container'></div
    </div>
</div>
<script>
    paypal.Buttons(
        {
            // Order is created on the server and the order id is returned
            createOrder(data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: <?= $order->total_price ?>,
                        }
                    }]
                });
            },
            //  Finalize the transaction on the server after payer approval
            onApprove(data, actions) {
                // captures the funds from the transaction.
                return actions.order.capture().then((details) => {
                    const $form = $('#checkout-form');
                    const paypalData = $form.serializeArray();
                    paypalData.push({
                        name: 'transactionId', value: details.id,
                    });
                    paypalData.push({
                        name: 'orderID', value: data.orderID,
                    });
                    paypalData.push({
                        name: 'status', value: details.status,
                    });
                    $.ajax({
                        method: 'post',
                        url: '<?= Url::to(['/cart/submit-payment', 'orderId' => $order->id])?>',
                        data: paypalData,
                        success: (res) => {
                            alert("<?= Yii::t('app', 'Thanks for your business') ?>");
                            window.location.href = '';
                        }
                    });
                });
            }
        }
    ).render('#paypal-button-container');
</script>