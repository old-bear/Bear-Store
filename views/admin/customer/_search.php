<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\CustomerSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="customer-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'open_id') ?>

    <?= $form->field($model, 'captcha') ?>

    <?= $form->field($model, 'subscribe_time') ?>

    <?= $form->field($model, 'refresh_token') ?>

    <?php // echo $form->field($model, 'nick_name') ?>

    <?php // echo $form->field($model, 'sexuality') ?>

    <?php // echo $form->field($model, 'head_img_path') ?>

    <?php // echo $form->field($model, 'balance') ?>

    <?php // echo $form->field($model, 'bonus_points') ?>

    <?php // echo $form->field($model, 'phone') ?>

    <?php // echo $form->field($model, 'email') ?>

    <?php // echo $form->field($model, 'qrcode_ticket') ?>

    <?php // echo $form->field($model, 'qrcode_expire_time') ?>

    <?php // echo $form->field($model, 'qrcode_url') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
