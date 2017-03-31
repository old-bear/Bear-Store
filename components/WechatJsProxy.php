<?php

namespace app\components;

use Yii;
use yii\base\Object;
use app\assets\WeAsset;
use app\models\JsapiTicket;
use app\components\CommonUtility;

class WechatJsProxy extends Object
{
    public $debug;
    public $appID;
    public $timestamp;
    public $nonceStr;
    
    public $url;
    public $view;
    public $ticket;

    public function __construct($config = [])
    {
        $this->debug = false;
        $this->timestamp = time();
        $this->nonceStr = CommonUtility::generateNonce();
        $this->ticket = JsapiTicket::getJsapiTicket();
        
        parent::__construct($config);
    }
    
    public function init()
    {
        parent::init();

        WeAsset::register($this->view);

        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $signStr = "jsapi_ticket={$this->ticket}&noncestr={$this->nonceStr}&"
                 . "timestamp={$this->timestamp}&url={$this->url}";
        $signature = sha1($signStr);
        $debug = ($this->debug ? 'true' : 'false');
        $configJs = <<<EOF
wx.config({
    debug: $debug,
    appId: "$this->appID",
    timestamp: $this->timestamp,
    nonceStr: "$this->nonceStr",
    signature: "$signature",
    jsApiList: [
        'onMenuShareTimeline',
        'onMenuShareAppMessage',
        'scanQRCode',
        'chooseWXPay',
    ]
});
EOF;
        $this->view->registerJs($configJs);
    }

    public function setShareInfo($title, $link, $imgUrl, $desc)
    {
        $shareJs = <<<EOF
var shareData = {
    title: '$title',
    link: '$link',
    imgUrl: '$imgUrl',
    desc: '$desc',
    success: function() {},
    cancel: function() {},
};
wx.onMenuShareTimeline(shareData); 
wx.onMenuShareAppMessage(shareData);
EOF;
        $this->view->registerJs($shareJs);
    }

    public function scanQRCode($orderID, $buttonID)
    {
        $scanJs = <<<EOF
var data = {
    needResult: 1,
    scanType: ['qrCode'],
    success: function(res) {
        $.post('/group-order/dispatch?id=$orderID',
               {'qrcode-url': res.resultStr}, function(res) {
            location.reload();
        });
    }
};
$('$buttonID').click(function() {
    wx.scanQRCode(data);
});
EOF;
        $this->view->registerJs($scanJs);
    }

    public function pay($prepayID, $redirectUrl, $buttonID)
    {
        $timestamp = time();
        $nonceStr = CommonUtility::generateNonce();
        $sign = Yii::$app->wepay->paymentJsSign($timestamp, $nonceStr, $prepayID);
        $payJs = <<<EOF
var paymentData = {
    appId: '$this->appID',
    timestamp: $timestamp,
    nonceStr: '$nonceStr',
    package: 'prepay_id=$prepayID',
    signType: 'MD5',
    paySign: '$sign',
    success: function(res) { window.location.href = "$redirectUrl"; },
    cancel: function() {},
};
$('$buttonID').click(function() {
    wx.chooseWXPay(paymentData);
});
EOF;
        $this->view->registerJs($payJs);
    }
}