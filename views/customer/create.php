<?php

/* @var $this yii\web\View */
/* @var $model app\models\Customer */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = '新会员注册';

$captchaTemplate = <<<EOF
<div class="input-group">
    {input}
    <span class="input-group-btn">
        <button class="btn btn-success" name="get-verification" id="get-verification">
            获取验证码
        </button>
    </span>
</div>
{error}
EOF;

$sendCaptcha = <<<EOF
$('#get-verification').click(function() {
    var phone = $('#customer-phone').val();
    $('#customer-inputcaptcha').val('');
    var url = '/customer/send-verification';
    var btn = $(this);

    $.post(url, {'phone': phone,}, 
           function(data) {
               // TODO: add callback function here
               console.log(data);
               var counter = 60;
               btn.prop('disabled', true);
               setInterval(function() {
                   counter--;
                   if (counter >= 0) {
                       $('#get-verification').html('重新获取(' + counter + 's)');
                   }
                   // Display 'counter' wherever you want to display it.
                   if (counter === 0) {
                       $('#get-verification').html('获取验证码');
                       clearInterval(counter);
                       btn.prop('disabled', false);
                   }
               }, 1000);
          }
    );
});
EOF;
$this->registerJs($sendCaptcha);

?>

<div class="container store-viewport">
    <?php $form = ActiveForm::begin(['id' => "user-form"]); ?>
    <div class="row">
        <div class="form-group col-xs-3 text-right required">
            <label class="control-label">手机号</label>
        </div>
        <div class="col-xs-9">
            <?= $form->field($model, 'phone')->label(false) ?>
        </div>
    </div>

    <div class="row">
        <div class="form-group col-xs-3 text-right required">
            <label class="control-label">验证码</label>
        </div>
        <div class="col-xs-9">
            <?php
            $captcha = $form->field($model, 'inputCaptcha');
            $captcha->template = $captchaTemplate;
            echo $captcha
            ?>
        </div>
    </div>

    <div class="row">
        <div class="form-group col-xs-3 text-right">
            <label class="control-label">邮箱</label>
        </div>
        <div class="col-xs-9">
            <?= $form->field($model, 'email')->label(false) ?>
        </div>
    </div>

    <div class="btn-group btn-group-justified">
        <div class="btn-group" role="group">
            <button type="submit" class="btn btn-danger">确认注册</button>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
