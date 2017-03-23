<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

$formatter = \Yii::$app->formatter;

$appId = $signPackage['appId'];
$timestamp = $signPackage['timestamp'];
$nonceStr = $signPackage['nonceStr'];
$signature = $signPackage['signature'];

$pay_timestamp = $paySignPackage['timestamp'];
$pay_nonceStr = $paySignPackage['nonceStr'];
$pay_package = $paySignPackage['package'];
$pay_paySign = $paySignPackage['paySign'];

$redirectURL = Url::to(['group-order/view', 'id'=> $order->group_order_id]);

$script = <<<EOF
var paymentData = {
    appId: '$appId',
    timestamp: '$pay_timestamp',
    nonceStr: '$pay_nonceStr',
    package: '$pay_package',
    signType: 'MD5',
    paySign: '$pay_paySign',
    success: function(res) { window.location.href = "$redirectURL"; },
    cancel: function() {},
}
var wechat = new WeChatInterface();
wechat.configure('$appId', $timestamp, '$nonceStr', '$signature', null, paymentData);

EOF;

$this->registerJs($script, View::POS_READY, 'amount-handler');

?>

<div class="container store-viewport" id="payment-info">
    <ul class="list-group">
        <li class="row list-group-item">
            <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                订单号
            </div>
            <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8" id="prepay_id">
                <span><?= Html::encode($order->pay_id) ?></span>
            </div>
        </li>
        <li class="row list-group-item">
            <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                支付金额
            </div>
            <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
                <?= $formatter->asCurrency($order->price) ?>
            </div>
        </li>
    </ul>
</div>