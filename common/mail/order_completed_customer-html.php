<?php
/** @var \common\models\Order $order */

$orderAddress = $order->orderAddress;
?>
<style>
    .row {
        display: flex;
    }
    .col {
        flex: 1;
    }
</style>
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
    </div>
</div>