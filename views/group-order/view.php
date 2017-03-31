<?php

/* @var $this yii\web\View */
/* @var $model app\models\Item */
/* @var $order app\models\GroupOrder */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use app\components\WechatJsProxy;

$url = Url::current([], true);
$formatter = \Yii::$app->formatter;

$wejs = new WechatJsProxy(['appID' => Yii::$app->utils->appID,
                           'url' => $url, 'view' => $this]);
$wejs->setShareInfo($model->name . ' - 快来友团拼团吧', $url,
                    Url::to($model->images[0]->photo_path, true), $model->description);
$wejs->scanQRCode($order->id, '#scan-qrcode');

$orderCss = <<<EOF
#item-order {
    line-height: 2;
}

#item-order .item-label {
    padding-top: 3px;
}

#item-amount .item-label {
    padding-top: 12px;
}

#item-amount .enough {
    color: green;
    padding-top: 10px;
}

#item-amount .lack {
    color: #f20c00;
    padding-top: 10px;
}
EOF;
$this->registerCss($orderCss);

$amountJs = <<<EOF
$("#amount").TouchSpin({
    initval: 1,
    min: 1,
    max: 100,
    buttondown_class: 'btn btn-default btn-sm',
    buttonup_class: 'btn btn-default btn-sm',
});
if ($("#amount").attr("quantity") === 0) {
    $("#amount").prop('disabled', true);
}
EOF;
$this->registerJs($amountJs);

$member = ($order && $order->status == 'delivered'
           && $order->leader_id == $user->id ? '_member_leader_view.php' : '_member.php');

$joinOrderUrl = "/customer-order/create?groupOrderID={$order->id}";

?>

<div class="container store-viewport">
    <?= $this->render('/item/_photo', [
        'model' => $model,
    ]) ?>

    <div class="row separator"></div>

    <?= $this->render('/item/_title', [
        'model' => $model,
    ]) ?>

    <div class="row separator"></div>

    <div id="item-order">
        <div id="item-amount" class="row">
            <div class="col-xs-2 item-label">数量</div>
            <div class="col-xs-5">
                <?= Html::beginForm("/customer-order/create?groupOrderID={$order->id}",
                                    'post', ['id' => 'join-form']) ?>
                <?= Html::input('number', 'amount', '1',
                                ['id' => 'amount',
                                 'class' => 'input-sm', 'quantity' => $model->amount]) ?>
                <?=Html::endForm() ?>
            </div>
            <?php
            if ($model->amount > 100) {
                echo '<div class="col-xs-5 enough">库存充足</div>';
            } else if ($model->amount > 0) {
                echo '<div class="col-xs-5 lack">库存紧张</div>';
            } else {
                echo '<div class="col-xs-5 lack">' . Html::encode('>_< 已售完') . '</div>';
            }
            ?>
        </div>
        <div class="row">
            <div class="col-xs-2 item-label">预计</div>
            <div class="col-xs-10"><?= $formatter->asDate($order->arrival_date) ?></div>
        </div>
        <div class="row">
            <div class="col-xs-2 item-label">送至</div>
            <div class="col-xs-10"><?= Html::encode($order->deliveryAddressString()) ?></div>
        </div>
    </div>

    <div class="row separator"></div>

    <?= $this->render($member, [
        'order' => $order,
        'user' => $user,
    ]) ?>
    
    <div class="row separator"></div>

    <?= $this->render('/item/_detail', [
        'model' => $model,
        'offset' => 2,
    ]) ?>
    
    <?= $this->render('_navbar', [
        'model' => $model,
        'order' => $order,
        'user' => $user,
    ]) ?>
    
    <div class="hidden">
        <div id="groupOrderId"><?= Html::encode($order->id) ?></div>
    </div>

</div>
