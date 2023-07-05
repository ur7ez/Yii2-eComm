<?php
/** @var \common\models\Order $order */

$orderAddress = $order->orderAddress;
?>
<?= Yii::t('app', 'Order') . ' #' . $order->id . ' ' . Yii::t('app', 'summary') ?>:

<?= Yii::t('app', 'Account Information') ?>
    <?= Yii::t('app', 'Firstname') . ': ' . $order->firstname ?>
    <?= Yii::t('app', 'Lastname') . ': ' . $order->lastname ?>
    <?= Yii::t('app', 'Email') . ': ' . $order->email ?>

<?= Yii::t('app', 'Address Information') ?>
    <?= Yii::t('app', 'Address') . ': ' . $orderAddress->address ?>
    <?= Yii::t('app', 'City') . ': ' . $orderAddress->city ?>
    <?= Yii::t('app', 'State') . ': ' . $orderAddress->state ?>
    <?= Yii::t('app', 'Country') . ': ' . $orderAddress->country ?>
    <?= Yii::t('app', 'ZipCode') . ': ' . $orderAddress->zipcode ?>

<?= Yii::t('app', 'Products') ?>
    <?= Yii::t('app', 'Name') ?>       <?= Yii::t('app', 'Quantity') ?>     <?= Yii::t('app', 'Price') ?>
<?php foreach ($order->orderItems as $item): ?>
    <?= $item->product_name ?>  <?= $item->quantity ?>    <?= Yii::$app->formatter->asCurrency($item->quantity * $item->unit_price) ?>
<?php endforeach; ?>

<?= Yii::t('app', 'Total Items') . ': ' . $order->getItemsQuantity() ?>
<?= Yii::t('app', 'Total Price') . ': ' . Yii::$app->formatter->asCurrency($order->total_price) ?>