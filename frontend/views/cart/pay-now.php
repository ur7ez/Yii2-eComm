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
        <h5>Account Information</h5>
        <table class="table">
            <tr>
                <th>Firstname</th>
                <td><?= $order->firstname ?></td>
            </tr>
            <tr>
                <th>Lastname</th>
                <td><?= $order->lastname ?></td>
            </tr>
            <tr>
                <th>Email</th>
                <td><?= $order->email ?></td>
            </tr>
        </table>
        <h5>Address Information</h5>
        <table class="table">
            <tr>
                <th>Address</th>
                <td><?= $orderAddress->address ?></td>
            </tr>
            <tr>
                <th>City</th>
                <td><?= $orderAddress->city ?></td>
            </tr>
            <tr>
                <th>State</th>
                <td><?= $orderAddress->state ?></td>
            </tr>
            <tr>
                <th>Country</th>
                <td><?= $orderAddress->country ?></td>
            </tr>
            <tr>
                <th>ZipCode</th>
                <td><?= $orderAddress->zipcode ?></td>
            </tr>
        </table>
    </div>
    <div class="col">
        <h5>Products</h5>
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
                <th>Total Items</th>
                <td><?= $order->getItemsQuantity() ?></td>
            </tr>
            <tr>
                <th>Total Price</th>
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
                            currency_code: 'USD',
                            value: <?= $order->total_price ?>,
                        }
                    }]
                });
                // return fetch("/my-server/create-paypal-order", {
                //     method: "POST",
                //     headers: {
                //         "Content-Type": "application/json",
                //     },
                //     // use the "body" param to optionally pass additional order information
                //     // like product skus and quantities
                //     body: JSON.stringify({
                //         cart: [
                //             {
                //                 sku: "YOUR_PRODUCT_STOCK_KEEPING_UNIT",
                //                 quantity: "YOUR_PRODUCT_QUANTITY",
                //             },
                //         ],
                //     }),
                // })
                //     .then((response) => response.json())
                //     .then((order) => order.id);
            },
            //  Finalize the transaction on the server after payer approval
            onApprove(data, actions) {
                console.log(data, actions);
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
                            alert('Thanks for your business');
                            window.location.href = '';
                        }
                    });
                });
                /*return fetch("/my-server/capture-paypal-order", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify({
                        orderID: data.orderID
                    })
                })
                    .then((response) => response.json())
                    .then((orderData) => {
                        // Successful capture! For dev/demo purposes:
                        console.log('Capture result', orderData, JSON.stringify(orderData, null, 2));
                        const transaction = orderData.purchase_units[0].payments.captures[0];
                        alert(`Transaction ${transaction.status}: ${transaction.id}\n
                    \nSee console for all available details`);
                        // When ready to go live, remove the alert and show a success message within this page. For example:
                        // const element = document.getElementById('paypal-button-container');
                        // element.innerHTML = '<h3>Thank you for your payment!</h3>';
                        // Or go to another URL:  window.location.href = 'thank_you.html';
                    });*/
            }
        }
    ).render('#paypal-button-container');
</script>