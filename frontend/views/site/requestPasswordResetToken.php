<?php
use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var \frontend\models\PasswordResetRequestForm $model */

$this->title = Yii::t('app', 'Request password reset');
?>
<div class="site-request-password-reset">
    <div class='row justify-content-center'>
        <div class='col-lg-6'>
            <h1><?= Html::encode($this->title) ?></h1>
            <p><?= Yii::t('app', 'Please fill out your email. A link to reset password will be sent there.') ?></p>
            <?php $form = ActiveForm::begin(['id' => 'request-password-reset-form']); ?>
            <?= $form->field($model, 'email')->textInput(['autofocus' => true]) ?>
            <div class="form-group">
                <?= Html::submitButton('Send', ['class' => 'btn btn-primary']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>