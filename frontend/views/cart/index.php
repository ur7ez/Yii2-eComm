<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** @var array $items */
?>
<div class="card">
    <div class="card-header">
        <h3>Your cart items</h3>
    </div>
    <div class="card-body p-0">
        <?php if (!empty($items)): ?>
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
                <tr data-id="<?= $item['id'] ?>" data-url="<?= Url::to(['/cart/change-quantity']) ?>">
                    <td><?= $item['name'] ?></td>
                    <td>
                        <img src="<?= \common\models\Product::formatImageUrl($item['image']) ?>"
                             style="width: 50px;"
                             alt="<?= $item['name'] ?>">
                    </td>
                    <td><?= Yii::$app->formatter->asCurrency($item['price']) ?></td>
                    <td>
                        <input type="number" min="1" class="form-control w-50 item-quantity" value="<?= $item['quantity'] ?>">
                    </td>
                    <td><?= Yii::$app->formatter->asCurrency($item['total_price']) ?></td>
                    <td>
                        <?= Html::a('Delete', ['/cart/delete', 'id' => $item['id']], [
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
            <a href="<?= Url::to(['/cart/checkout']) ?>" class="btn btn-primary">Checkout</a>
        </div>
        <?php else: ?>
        <p class="text-muted text-center p-5">There are no items in the cart</p>
        <?php endif; ?>
    </div>
</div>