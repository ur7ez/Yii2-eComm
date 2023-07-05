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
<h3><?= Yii::t('app', 'Order') ?> #<?= $order->id ?> <?= Yii::t('app', 'summary') ?>:</h3>
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
    </div>
</div>