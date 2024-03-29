<?php
use yii\widgets\Pjax;

/** @var \yii\web\View $this */
/** @var \common\models\User $user */
/** @var \common\models\UserAddress $userAddress */
?>

<div id="page-profile" class="row">
    <div class="col">
        <div class="card">
            <div class="card-header">
                <?= Yii::t('app', 'Address Information') ?>
            </div>
            <div class="card-body">
                <!-- Address information form -->
                <?php Pjax::begin([
                    'enablePushState' => false,
                ]) ?>
                <?= $this->render('user_address', [
                    'userAddress' => $userAddress,
                ]) ?>
                <?php Pjax::end(); ?>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card">
            <div class="card-header">
                <?= Yii::t('app', 'Account Information') ?>
            </div>
            <div class="card-body">
                <!-- Account information form -->
                <?php Pjax::begin([
                    'enablePushState' => false,
                ]) ?>
                <?= $this->render('user_account', [
                    'user' => $user,
                ]) ?>
                <?php Pjax::end(); ?>
            </div>
        </div>
    </div>
</div>