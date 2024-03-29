<?php
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap5\ActiveForm */
/* @var $model \backend\models\PasswordResetRequestForm */

$this->title = 'Reset password';
?>
<div class="row">
    <div class="col-lg-6 d-none d-lg-block bg-login-image"
         style="background-size: contain; background-repeat: no-repeat; background-image: url('/img/undraw_web_shopping_dd4l.svg')"></div>
    <div class="col-lg-6">
        <div class="p-5">
            <div class="text-center">
                <h1 class="h4 text-gray-900 mb-4">Reset your password</h1>
            </div>
            <?php $form = ActiveForm::begin([
                'id' => 'forgot-password-form',
                'options' => ['class' => 'user']
            ]); ?>

            <?= $form->field($model, 'password', [
                'inputOptions' => [
                    'class' => 'form-control form-control-user',
                    'placeholder' => 'Enter your new password',
                ]
            ])->passwordInput(['autofocus' => true]) ?>

            <?= Html::submitButton('Submit', ['class' => 'btn btn-primary btn-user btn-block', 'name' => 'resetpwd-button']) ?>
            <?php ActiveForm::end() ?>
            <hr>
            <div class="text-center">
                <a class="small" href="<?php echo \yii\helpers\Url::to(['/site/login']) ?>">Login</a>
            </div>
        </div>
    </div>
</div>