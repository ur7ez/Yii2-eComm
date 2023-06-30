<?php
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var \backend\models\PasswordResetRequestForm $model */

$this->title = 'Forgot password?';
?>
<div class="row">
    <div class="col-lg-6 d-none d-lg-block bg-login-image"
         style="background-size: contain; background-repeat: no-repeat; background-image: url('/img/undraw_web_shopping_dd4l.svg')">
    </div>
    <div class="col-lg-6">
        <div class="p-5">
            <div class="text-center">
                <h1 class="h4 text-gray-900 mb-4">Forgot your password?</h1>
            </div>
            <?php $form = ActiveForm::begin([
                'id' => 'forgot-password-form',
                'options' => ['class' => 'user'],
            ]); ?>
            <?= $form->field($model, 'email', [
                'inputOptions' => [
                    'class' => 'form-control form-control-user',
                    'placeholder' => 'Enter your email'
                ]
            ])->textInput(['autofocus' => true]) ?>
            <?= Html::submitButton('Submit', ['class' => 'btn btn-primary btn-user btn-block', 'name' => 'submit-button']) ?>
            <?php ActiveForm::end() ?>
            <hr>
            <div class="text-center">
                <a class="small" href="<?= \yii\helpers\Url::to(['/site/login']) ?>">Login</a>
            </div>
        </div>
    </div>
</div>