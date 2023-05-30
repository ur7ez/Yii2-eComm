<?php
/** @var array $items */
?>
<div class="card">
    <div class="card-header">
        <h3>Your cart items</h3>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover">
            <thead>
            <tr>
                <th>Product</th>
                <th>Image</th>
                <th>Unit Price</th>
                <th>Quantity</th>
                <th>Total Price</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= $item['name'] ?></td>
                    <td>
                        <img src="<?= \common\models\Product::formatImageUrl($item['image']) ?>"
                             style="width: 50px;"
                             alt="<?= $item['name'] ?>">
                    </td>
                    <td><?= $item['price'] ?></td>
                    <td><?= $item['quantity'] ?></td>
                    <td><?= $item['total_price'] ?></td>
                    <td>
                        <?= \yii\helpers\Html::a('Delete', ['/cart/delete', 'id' => $item['id']], [
                            'class' => 'btn btn-outline-danger btn-sm',
                            'data-method' => 'post',
                            'data-confirm' => 'Are you sure you want to remove this product from cart?',
                        ]) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <div class="card-body text-end">
            <a href="<?= \yii\helpers\Url::to(['/cart/checkout']) ?>" class="btn btn-primary">Checkout</a>
        </div>
    </div>
</div>