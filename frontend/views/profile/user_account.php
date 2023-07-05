<?php
use yii\bootstrap5\ActiveForm;

/** @var \yii\web\View $this */
/** @var \common\models\User $user */
?>
<?php if (isset($success) && $success): ?>
    <div class="alert alert-success">
        <?= Yii::t('app', 'Your account was successfully updated') ?>
    </div>
<?php endif; ?>
<?php $form = ActiveForm::begin([
    'action' => ['/profile/update-account'],
    'options' => [
        'data-pjax' => 1,
    ],
]); ?>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($user, 'firstname')->textInput(['autofocus' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($user, 'lastname')->textInput(['autofocus' => true]) ?>
        </div>
    </div>
    <?= $form->field($user, 'username')->textInput(['autofocus' => true]) ?>
    <?= $form->field($user, 'email') ?>
    <div class="row">
        <div class="col">
            <?= $form->field($user, 'password')->passwordInput(['autocomplete' => 'new-password']) ?>
        </div>
        <div class="col">
            <?= $form->field($user, 'password_repeat')->passwordInput() ?>
        </div>
    </div>
    <button class="btn btn-primary"><?= Yii::t('app', 'Update') ?></button>
<?php ActiveForm::end(); ?>