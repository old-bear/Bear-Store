<?php

namespace app\components;

use Yii;
use yii\base\Object;
use yii\helpers\VarDumper;
use app\components\InternalException;

class CommonUtility extends Object
{
    public $appID;
    public $appSecret;
  
    public $merchantID;
    public $paymentKey;

    public function generateOAuthURL(
        string $url, string $scope = 'snsapi_base', string $state = '')
    {
        return 'https://open.weixin.qq.com/connect/oauth2/authorize?'
            . 'appid=' . $this->appID
            . '&redirect_uri=' . urlencode($url)
            . '&response_type=code&scope=' . $scope
            . '&state=' . ($state ? $state : $scope) . '#wechat_redirect';
    }

    public function fetchOAuthAccessToken(string $code)
    {
        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?'
             . 'appid=' . $this->appID
             . '&secret=' . $this->appSecret
             . '&code='. $code . '&grant_type=authorization_code';
        return self::sendHttpRequest('GET', $url, 'OAUTH_AK', true);        
    }

    public static function fetchOAuthUserInfo(string $ak, string $openID,
                                              string $lang = 'zh_CN')
    {
        $url = 'https://api.weixin.qq.com/sns/userinfo?'
             . 'access_token=' . $ak . '&openid=' . $openID . '&lang=' . $lang;
        return self::sendHttpRequest('GET', $url, 'OAUTH_USER_INFO', true);
    }

    public function fetchAccessToken()
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/token?'
             . 'grant_type=client_credential'
             . '&appid=' . $this->appID . '&secret=' . $this->appSecret;
        return self::sendHttpRequest('GET', $url, 'AK', true);
    }
  
    public function fetchJsApiTicket(string $ak)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?'
             . 'type=jsapi&access_token=' . $ak;
        return self::sendHttpRequest('GET', $url, 'JSAPI', true);
    }
  
    public function fetchQrTicket(string $ak, string $id)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=' . $ak;
        $data = [
            'expire_seconds' => 2592000,
            'action_name' => 'QR_SCENE',
            'action_info' => [
                'scene' => [
                    'scene_id' => $id,
                ],
            ],
        ];
        return self::sendHttpRequest('POST', $url, 'QRCODE', true, 1000, 0, [], $data, 'json');
    }
  
    // 微信JSAPI(configuration)签名
    public function getSignPackage(string $ak, string $ticket)
    {
        // 注意 URL 一定要动态获取，不能 hardcode.
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        $timestamp = time();
        $nonceStr = self::createNonceStr();

        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $string = "jsapi_ticket=$ticket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

        $signature = sha1($string);

        $signPackage = array(
            "appId"     => $this->appID,
            "nonceStr"  => $nonceStr,
            "timestamp" => $timestamp,
            "url"       => $url,
            "signature" => $signature,
            "rawString" => $string
        );
        return $signPackage; 
    }
    
    // 微信支付JSAPI签名
    public function getWxPaySignPackage($prepay_id)
    {
        $timestamp = time();
        $nonceStr = self::createNonceStr();
        $package = "prepay_id=" . $prepay_id;
        
        $string = "appId=$this->appID&nonceStr=$nonceStr&package=$package&signType=MD5&timeStamp=$timestamp&key=$this->paymentKey";
      
        $paySign = strtoupper(md5($string));
      
        $paySignPackage = array(
            'timestamp' => $timestamp,
            'nonceStr' => $nonceStr,
            'package' => $package,
            'paySign' => $paySign
        );
      
        return $paySignPackage;
    }
  
    public function generateUnifiedOrderData($order, $user_ip, $openid)
    {
        $nonceStr =  self::createNonceStr();
        $data = array(
            "appid" => $this->appID,
            "mch_id" => $this->merchantID,
            "nonce_str" => $nonceStr,
            "body" => "从前有个团-" . $order->item->name,
            "out_trade_no" => $order->id,
            "total_fee" => (int)($order->price * 100),
            "spbill_create_ip" => $user_ip,
            "notify_url" => "http://www.52youtuan.com/customer-order/notify",
            "trade_type" => "JSAPI",
            "openid" => $openid
        );
      
        $sign = self::generatePaymentSignature($data, $this->paymentKey);
        $data['sign'] = $sign;
      
        $xml = new \SimpleXMLElement('<xml></xml>');
        self::array_to_xml($data, $xml);
        return $xml;
    }
  
    public function generateRefundData($order)
    {
        $nonceStr =  self::createNonceStr();
        $data = array(
            "appid" => $this->appID,
            "mch_id" => $this->merchantID,
            "nonce_str" => $nonceStr,
            "transaction_id" => $order->pay_id,
            "out_refund_no" => $order->id,
            "total_fee" => (int)($order->price * 100),
            "refund_fee" => (int)($order->price * 100),
            "op_user_id" => $this->merchantID,
        );
      
        $sign = self::generatePaymentSignature($data, $this->paymentKey);
        $data['sign'] = $sign;
      
        $xml = new \SimpleXMLElement('<xml></xml>');
        self::array_to_xml($data, $xml);
        return $xml;
    }
  
    public function postUnifiedOrderRequest($xml)
    {
        $url = "https://api.mch.weixin.qq.com/pay/unifiedorder";
        $body = array(
            "xml" => $xml,
        );
        return self::sendHttpRequest('POST', $url, 'UNIFIEDORDER', false, 
                              1000, 0, [], $body, 'xml');
    }
  
    public function postRefundRequest($xml)
    {
        $url = "https://api.mch.weixin.qq.com/secapi/pay/refund";
        $vars = $xml->asXML();
        $aHeader = array(
            'Content-Type' => 'text/xml',
        );
        return self::curl_post_ssl($url, $vars, 30, $aHeader);
    }
  
    public function wechatNotifyResponse(string $return_code, string $return_msg)
    {
        $data = array(
            'return_code' => $return_code,
            'return_msg' => $return_msg,
        );
      
        $xml = new \SimpleXMLElement('<xml></xml>');
        self::array_to_xml($data, $xml, true);  
        
        return $xml->asXML();
    }
  
    public function parseResponseXML(string $str)
    {
        $xml = simplexml_load_string($str, 'SimpleXMLElement', LIBXML_NOCDATA);  
        $xml_file_arr = json_decode(json_encode($xml), true);
        return $xml_file_arr;
    }
  
    public static function array_to_xml( $data, &$xml_data, $add_cdata = false) {
        foreach( $data as $key => $value ) {
            if ($add_cdata) {
                $val = '![CDATA[' . htmlspecialchars("$value") . ']]';
                $xml_data->addChild("$key", $val);
            } else {
                $xml_data->addChild("$key", htmlspecialchars("$value"));
            }
        }
    }
  
    public static function array2xml(array $data,
                                     \SimpleXMLElement &$node = null,
                                     array $cdataKey = [])
    {
        // Compare with null explicitly since node
        // with empty tags is regarded as false too
        if ($node == null) {
            $node = new \SimpleXMLElement('<xml></xml>');
        }
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $subnode = $node->addChild($key);
                self::array2xml($value, $subnode, $cdataKey);
            } else {
                if (in_array($key, $cdataKey)) {
                    $node->addChild($key, "![CDATA[$value]]");
                } else {
                    $node->addChild($key, htmlspecialchars($value));
                }
            }
        }
        return $node;
    }

    public static function xml2array(string $xmlStr)
    {
        return (array)simplexml_load_string($xmlStr,
                                            'SimpleXMLElement', LIBXML_NOCDATA);  
    }
    
    public static function createNonceStr($length = 16)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
          $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }
  
    public static function generateNonce($length = 16)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
          $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }
  
    public static function generatePaymentSignature($data, $key)
    {
        $str = self::createQueryString($data);
        $str .= 'key=' . $key;
        $str = strtoupper(md5($str));
        return $str;
    }
  
    public static function createQueryString($data) 
    {
        $str = "";
        ksort($data);
        foreach ($data as $key => $val) {
            $str .= $key . '=' . $val . '&';
        }
        return $str;
    }

    public static function fetchUserInfo(string $ak, string $openID,
                                         string $lang = 'zh_CN')
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/user/info?'
             . 'access_token=' . $ak . '&openid=' . $openID . '&lang=' . $lang;
        return self::sendHttpRequest('GET', $url, 'USER_INFO', true);
    }
  
    public static function sendHttpRequest(string $method, string $url,
                                           string $logPrefix = 'HTTP',
                                           bool $forceJsonParse = false,
                                           int $timeoutMs = 1000, int $retry = 0,
                                           array $reqHeader = [], $reqBody = [],
                                           string $reqDataFormant = 'json')                                           
    {
        $ch = curl_init($url);
        if ($ch === false) {
            throw new InternalException('Failed to create curl handle to ' . $url);
        }

        $ret = ['header' => []];
        $options = [
            CURLOPT_TIMEOUT_MS => $timeoutMs,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADERFUNCTION => function ($ch, $header) use(&$ret) {
                $pos = strpos($header, ':');
                if ($pos !== false) {
                    $key = strtolower(trim(substr($header, 0, $pos)));
                    $ret['header'][$key] = trim(substr($header, $pos + 1));
                }
                return strlen($header);
            },
        ];
        if ($reqBody) {
            // Assume using JSON format
            if ($reqDataFormant == 'xml') {
                $reqHeader['Content-Type'] = 'text/xml';
                $options[CURLOPT_POSTFIELDS] = $reqBody->asXML();
            } else if ($reqDataFormant == 'json') {
                $reqHeader['Content-Type'] = 'application/json';
                $options[CURLOPT_POSTFIELDS] = json_encode($reqBody);
            } else {
                $options[CURLOPT_POSTFIELDS] = $reqBody;
            }
        }
        if ($reqHeader) {
            $options[CURLOPT_HTTPHEADER] = $reqHeader;
        }
        if (curl_setopt_array($ch, $options) === false) {
            throw new InternalException('Failed to set curl options '
                                        . VarDumper::export($options));
        }

        Yii::trace("[${logPrefix}_REQUEST] [URL=$url]"
                   . ($reqHeader ? ' [HEADER=' . VarDumper::export($reqHeader) . ']' : '')
                   . ($reqBody ? ' [BODY=' . $options[CURLOPT_POSTFIELDS] . ']' : ''), 'service');
        for ($i = 0; $i <= $retry; ++$i) {
            $start = microtime(true);
            $response = curl_exec($ch);
            $elapsedMs = (microtime(true) - $start) * 1000;
            if ($response === false) {
                Yii::error("[${logPrefix}_RESPONSE FAILED] [ELAPSED=${elapsedMs}ms]"
                           . " [REASON= " . curl_error($ch) . ']');
                $errno = curl_errno($ch);
                if ($errno == 6          // CURLE_COULDNT_RESOLVE_HOST
                    || $errno == 7       // CURLE_COULDNT_CONNECT
                    || $errno == 55      // CURLE_SEND_ERROR
                    || $errno == 56      // CURLE_RECV_ERROR
                ) {
                    // Retry
                    continue;
                }
            } else {
                $ret['info'] = curl_getinfo($ch);
                $code = $ret['info']['http_code'];
                if ($code >= 400) {
                    Yii::error("[${logPrefix}_RESPONSE FAILED] [ELAPSED=${elapsedMs}ms]"
                               . " [CODE=$code]" . ($response ? " [BODY=$response]" : ''));
                } else {
                    if ($forceJsonParse
                        || strcasecmp($ret['info']['content_type'], 'application/json') == 0) {
                        $ret['body'] = json_decode($response, true);
                    } else {
                        $ret['body'] = $response;
                    }
                    Yii::trace("[${logPrefix}_RESPONSE SUCCEEDED] [ELAPSED=${elapsedMs}ms] [RESPONSE"
                               . (strlen($response) < 256 ? "=" . VarDumper::export($ret['body'])
                                  : '_LENGTH=' . strlen($response)) . ']', 'service');
                }
            }
            break;
        }
        return $ret;
    }
  
    function curl_post_ssl($url, $vars, $second=30, $aHeader=array())
    {
        $ch = curl_init();
        //超时时间
        curl_setopt($ch,CURLOPT_TIMEOUT,$second);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
        //这里设置代理，如果有的话
        //curl_setopt($ch,CURLOPT_PROXY, '10.206.30.98');
        //curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);

        //以下两种方式需选择一种

        //第一种方法，cert 与 key 分别属于两个.pem文件
        //默认格式为PEM，可以注释
        curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
        curl_setopt($ch,CURLOPT_SSLCERT,getcwd().'/apiclient_cert.pem');
        //默认格式为PEM，可以注释
        curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
        curl_setopt($ch,CURLOPT_SSLKEY,getcwd().'/apiclient_key.pem');

        //第二种方式，两个文件合成一个.pem文件
        //curl_setopt($ch,CURLOPT_SSLCERT,getcwd().'/all.pem');

        if( count($aHeader) >= 1 ){
          curl_setopt($ch, CURLOPT_HTTPHEADER, $aHeader);
        }

        curl_setopt($ch,CURLOPT_POST, 1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$vars);
        $data = curl_exec($ch);
        if($data){
            curl_close($ch);
            return $data;
        }
        else { 
            $error = curl_errno($ch);
            echo "call faild, errorCode:$error\n"; 
            curl_close($ch);
            return false;
        }
    }
}