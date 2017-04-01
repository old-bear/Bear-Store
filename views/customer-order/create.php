<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use app\components\WechatJsProxy;

$url = Url::current([], true);
$redirectUrl = Url::to(['group-order/view', 'id'=> $order->group_order_id]);
$formatter = \Yii::$app->formatter;

$wejs = new WechatJsProxy(['appID' => Yii::$app->utils->appID,
                           'url' => $url, 'view' => $this]);
$wejs->pay($order->prepay_id, $redirectUrl, '#start-pay');
$wejs->commit();

?>

<div class="container store-viewport">
    <h1 class="text-center">订单详情</h1>
    <div class="weui-form-preview">
        <div class="weui-form-preview__hd">
            <div class="weui-form-preview__item">
                <label class="weui-form-preview__label">付款金额</label>
                <em class="weui-form-preview__value">
                    <?= $formatter->asCurrency($order->price) ?>
                </em>
            </div>
        </div>

        <div class="weui-form-preview__bd">
            <div class="weui-form-preview__item">
                <label class="weui-form-preview__label">商品</label>
                <span class="weui-form-preview__value">
                    <?= Html::encode($order->item->name) ?>
                </span>
            </div>
            <div class="weui-form-preview__item">
                <label class="weui-form-preview__label">单价</label>
                <span class="weui-form-preview__value">
                    <?= $formatter->asCurrency($order->item->price) ?>
                </span>
            </div>
            <div class="weui-form-preview__item">
                <label class="weui-form-preview__label">数量</label>
                <span class="weui-form-preview__value">
                    <?= Html::encode($order->amount) ?>
                </span>
            </div>
        </div>

        <div class="weui-form-preview__ft">
            <a class="weui-form-preview__btn weui-form-preview__btn_primary"
               id="start-pay" href="javascript:;">确认支付</a>
        </div>
    </div>
</div>
