<?php

/* @var $this yii\web\View */
/* @var $order app\models\GroupOrder */

use yii\helpers\Html;
use yii\helpers\Url;

$item = $order->item;
$leader = $order->leader;
$orderLink = '/group-order/view?id=' . $order->id;

?>

<div class="weui-panel weui-panel_access">
    <div class="weui-panel__hd">
        <div class="weui-cell">
            <div class="weui-cell__hd">团购订单</div>
            <div class="weui-cell__bd"><?= Html::encode($order->id) ?></div>
            <div class="weui-cell__ft">团长：<?= Html::encode($leader->nick_name) ?></div>
        </div>
    </div>
    <div class="weui-panel__bd">
        <a href="<?= $orderLink ?>" class="weui-media-box weui-media-box_appmsg">
            <div class="weui-media-box__hd">
                <?= Html::img($item->images[0]->photo_path) ?>
            </div>
            <div class="weui-media-box__bd">
                <div class="weui-cell">
                    <h4 class="weui-cell__bd"><?= Html::encode($item->name) ?></h4>
                    <div class="weui-cell__ft"><?= Html::encode($order->statusString) ?></div>
                </div>
            </div>
        </a>
    </div>
    <div class="weui-panel__ft">
        <?php foreach ($order->getCustomerOrder($user->id) as $co): ?>
            <div class="weui-cell weui-cell_link">
                <span class="glyphicon glyphicon-barcode weui-cell__hd"></span>
                <div class="weui-cell__bd"><?= Html::encode($co->transaction_id) ?></div>
                <div class="weui-cell__ft">数量：<?= Html::encode($co->amount) ?></div>
            </div>
        <?php endforeach; ?>
    </div>
</div>               
