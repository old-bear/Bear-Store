<?php

/* @var $this yii\web\View */
/* @var $item app\models\Item */
/* @var $user app\models\Customer */
/* @var $order app\models\CustomerOrder */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

$amount = $order->amount;
$total_price = $order->price;
$price = $item->price;

$submitUrl = Url::to(['customer-order/submit', 'orderID' => $order->group_order_id]);
$formatter = \Yii::$app->formatter;

$currentUrl = Url::current(['amount' => null]);

$script = <<<EOF
$(function() {
    $('#amount').on('change', function() {
        var amount = $('#amount').val();
        var price = amount * $price;
        var html_str = $('#total_fee').html();
        $('#total_fee').html(html_str.substring(0,2) + price.toFixed(2));
    });
});
EOF;

$this->registerJs($script, View::POS_READY, 'amount-handler');

?>

<div class="container store-viewport">
    <?= $this->render('/item/_photo.php', [
        'model' => $item,
    ]) ?>
  
    <?= $this->render('/item/_title.php', [
        'model' => $item,
    ]) ?>
  
    <div class="row bg-info separator"></div>
    
    <?php $form = ActiveForm::begin(['id' => 'customer-order-form', 'action' => $submitUrl]); ?>
    <div class="row">
        <div class="order-label col-xs-3">订购数量</div>
        <div class="col-xs-9">
            <?= Html::textInput('amount', $amount, ['id' => 'amount']) ?>
        </div>
    </div>
  
    <div id="customer-order-navbar">
        <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8 text-right payment-summary">
            <span class="payment-label">实付款: </span>
            <span class="payment-currency" id="total_fee"> <?= $formatter->asCurrency($total_price) ?> </span>
        </div>
        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 text-center submit-btn-wrapper">
            <button type="submit" name="submit-customer-order" value="1" class="btn btn-danger">立即支付</button>
        </div>
    </div>
  
    <?php ActiveForm::end(); ?>
  
    
    
</div>