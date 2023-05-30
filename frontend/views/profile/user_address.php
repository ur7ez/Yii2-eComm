<?php
use yii\bootstrap5\ActiveForm;

/** @var \yii\web\View $this */
/** @var \common\models\UserAddress $userAddress */
?>
<?php if (isset($success) && $success): ?>
    <div class="alert alert-success">
        Yor address was successfully updated
    </div>
<?php endif; ?>

<?php $addressForm = ActiveForm::begin([
    'action' => ['/profile/update-address'],
    'options' => [
        'data-pjax' => 1,
    ],
]); ?>
    <?= $addressForm->field($userAddress, 'address')->textInput(['autofocus' => true]) ?>
    <?= $addressForm->field($userAddress, 'city')->textInput(['autofocus' => true]) ?>
    <?= $addressForm->field($userAddress, 'state')->textInput(['autofocus' => true]) ?>
    <?= $addressForm->field($userAddress, 'country')->textInput(['autofocus' => true]) ?>
    <?= $addressForm->field($userAddress, 'zipcode')->textInput(['autofocus' => true]) ?>
    <button class="btn btn-primary">Update</button>
<?php ActiveForm::end(); ?>