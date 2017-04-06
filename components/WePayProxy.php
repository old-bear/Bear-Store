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
    public $apiclientCert;
    
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
        $req['sign'] = $this->paymentSign($req);

        $url = "https://api.mch.weixin.qq.com/pay/unifiedorder";
        $xml = CommonUtility::array2xml($req);
        $res = CommonUtility::sendHttpRequest("POST", $url, 'UNIFIED_ORDER',
                                              false, 1000, 0, [], $xml, 'xml');
        return CommonUtility::xml2array($res['body']);
    }

    public function refund(CustomerOrder $order)
    {
        $nonceStr = CommonUtility::generateNonce();
        $req = [
            "appid" => $this->appID,
            "mch_id" => $this->merchantID,
            "nonce_str" => $nonceStr,
            "transaction_id" => $order->transaction_id,
            "out_refund_no" => $order->id,
            "total_fee" => (int)($order->price * 100),
            "refund_fee" => (int)($order->price * 100),
            "op_user_id" => $this->merchantID,
        ];
        $req['sign'] = $this->paymentSign($req);

        $url = "https://api.mch.weixin.qq.com/secapi/pay/refund";
        $xml = CommonUtility::array2xml($req);
        $res = CommonUtility::sendHttpRequest("POST", $url, 'REFUND', false,
                                              1000, 0, [], $xml, 'xml',
                                              $this->apiclientCert);
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