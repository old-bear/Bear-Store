<?php

/* @var $this yii\web\View */
/* @var $model app\models\Item */
/* @var $order app\models\GroupOrder */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

use yii\widgets\ListView;

$formatter = \Yii::$app->formatter;

$count = 0;
$maximun_count = 3;

$appId = $signPackage['appId'];
$timestamp = $signPackage['timestamp'];
$nonceStr = $signPackage['nonceStr'];
$signature = $signPackage['signature'];

$shareTitle = $model->name;
$shareDesc = $model->description;
$shareLink = Url::current([], true);
$shareImgUrl = Url::base(true) . $model->images[0]->photo_path;

$script = <<<EOF
var shareData = {
    title: '$shareTitle',
    link: '$shareLink',
    imgUrl: '$shareImgUrl',
    desc: '$shareDesc',
    success: function() {},
    cancel: function() {},
}
var wechat = new WeChatInterface();
wechat.configure('$appId', $timestamp, '$nonceStr', '$signature', shareData);

$('#scan-qrcode').click(function() {
    wechat.scanQRCode();
})

EOF;

$this->registerJs($script, View::POS_READY, 'amount-handler');

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
                <?= Html::textInput('amount', '1', ['id' => 'amount',
                                                    'quantity' => $model->amount]) ?>
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
    
    <div id="group-detail">
        <?php if ($order->status === 'delivered' or $order->status === 'completed'): ?>
        <div class="row delivered-row">
        <?php else: ?>
        <div class="row">
        <?php endif; ?>
            <div class="col-xs-2 item-label">成员</div>
            <div class="col-xs-3">
                <?= Html::img($order->leader->head_img_path, ['id' => "head-img"]) ?>
            </div>
            <div class="col-xs-4">
                <div class="row"><?= Html::encode($order->leader->nick_name) ?></div>
                <hr class="row"></hr>
                <div class="row"><?= Html::encode($order->leader->phone) ?></div>
            </div>
            <div class="col-xs-3">
                <?php if ($order->status === 'delivered' or $order->status === 'completed'): ?>
                <span class="glyphicon glyphicon-ok delivered-icon" aria-hidden="true"> </span>
                <?php else: ?>
                <img id="leader-img" src="/images/leader.png" />
                <?php endif; ?>
            </div>
        </div>
  
        <?php foreach ($order->members as $member): ?>
            <?php if ($member->id != $order->leader->id): ?>
                <?php if ($member->customerOrdersAreDelivered($order->id)): ?>
                <div class="row delivered-row <?php if ($count >= $maximun_count): echo "hidden-row"; endif; ?>">
                <?php else: ?>
                <div class="row <?php if ($count >= $maximun_count): echo "hidden-row"; endif; ?>">
                <?php endif; ?>
                    <div class="col-xs-2 item-label"></div>
                    <div class="col-xs-3">
                        <?= Html::img($member->head_img_path, ['id' => "head-img"]) ?>
                    </div>
                    <div class="col-xs-4">
                        <div class="row"><?= Html::encode($member->nick_name) ?></div>
                        <hr class="row"></hr>
                        <div class="row"><?= Html::encode($member->phone) ?></div>
                    </div>
                    <div class="col-xs-3">
                        <?php if ($member->customerOrdersAreDelivered($order->id)): ?>
                        <span class="glyphicon glyphicon-ok delivered-icon" aria-hidden="true"> </span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php $count++; endif; ?>
        <?php endforeach; ?>
  
        <?php if ($count >= $maximun_count): ?>
            <button id="display-all"> 展开全部 </button>
        <?php endif; ?>
    </div>

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
