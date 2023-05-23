<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use dosamigos\ckeditor\CKEditor;

/** @var yii\web\View $this */
/** @var common\models\Product $model */
/** @var yii\bootstrap5\ActiveForm $form */
?>

<div class="product-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->widget(CKEditor::class, [
        'options' => ['rows' => 6],
        'preset' => 'basic'
    ]) ?>

    <?= $form->field($model, 'imageFile', [
            'template' => '
                <div class="input-group mb-3">
                    {label}
                    {input}
                    {error}
                </div>
            ',
        'inputOptions' => ['class' => 'form-control'],
        'labelOptions' => ['class' => 'input-group-text'],
    ])->fileInput(['type' => 'file']) ?>

    <?= $form->field($model, 'price')->textInput([
            'maxlength' => true,
            'type' => 'number',
    ]) ?>

    <?= $form->field($model, 'status')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>