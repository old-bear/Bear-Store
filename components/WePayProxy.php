<?php

namespace app\components;

use Yii;
use yii\base\Object;
use app\models\JsapiTicket;
use app\models\CustomerOrder;
use app\components\CommonUtility;

class WePayProxy extends Object
{
    public $appID;
    public $merchantID;
    public $paymentKey;
    
    public function unifiedOrder(CustomerOrder $order, $userIp, $openID)
    {
        $nonceStr = CommonUtility::generateNonce();
        $req = [
            "appid" => $this->appID,
            "mch_id" => $this->merchantID,
            "nonce_str" => $nonceStr,
            "body" => "从前有个团-" . $order->item->name,
            "out_trade_no" => $order->id,
            "total_fee" => (int)($order->price * 100),
            "spbill_create_ip" => $userIp,
            "notify_url" => "http://www.52youtuan.com/customer-order/notify",
            "trade_type" => "JSAPI",
            "openid" => $openID,
        ];
        $sign = CommonUtility::generatePaymentSignature($req, $this->paymentKey);
        $req['sign'] = $sign;

        $url = "https://api.mch.weixin.qq.com/pay/unifiedorder";
        $xml = CommonUtility::array2xml($req);
        $res = CommonUtility::sendHttpRequest("POST", $url, 'UNIFIED_ORDER',
                                              false, 1000, 0, [], $xml, 'xml');
        return CommonUtility::xml2array($res['body']);
    }

    public function paymentJsSign($timestamp, $nonceStr, $prepayID)
    {
        $package = "prepay_id=$prepayID";
        $signStr = "appId={$this->appID}&nonceStr=$nonceStr&package=$package&"
                 . "signType=MD5&timeStamp=$timestamp&key={$this->paymentKey}";
        return strtoupper(md5($signStr));
    }

    public function verifySign(array $body, string $sign)
    {
        return $this->paymentSign($body) == $sign;
    }
    
    private function paymentSign(array $body)
    {
        $signStr = "";
        ksort($body);
        foreach ($body as $key => $val) {
            $signStr .= ($key . '=' . $val . '&');
        }
        $signStr .= ('key=' . $this->paymentKey);
        return strtoupper(md5($signStr));
    }
}