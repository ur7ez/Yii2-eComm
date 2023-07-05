<?php
use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var \frontend\models\ResetPasswordForm $model */

$this->title = Yii::t('app', 'Resend verification email');
?>
<div class="site-resend-verification-email">
    <div class='row justify-content-center'>
        <div class='col-lg-6'>
            <h1><?= Html::encode($this->title) ?></h1>
            <p><?= Yii::t('app', 'Please fill out your email. A verification email will be sent there.') ?></p>
            <?php $form = ActiveForm::begin(['id' => 'resend-verification-email-form']); ?>
            <?= $form->field($model, 'email')->textInput(['autofocus' => true]) ?>
            <div class="form-group">
                <?= Html::submitButton('Send', ['class' => 'btn btn-primary']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>