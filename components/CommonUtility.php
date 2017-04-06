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
    
    public static function generateNonce($length = 16)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
          $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
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
                                           string $reqDataFormant = 'json',
                                           string $clientCert = '')
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
        if ($clientCert) {
            $options[CURLOPT_SSLCERT] = $clientCert;
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
}