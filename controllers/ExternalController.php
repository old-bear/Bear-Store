<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\helpers\VarDumper;
use app\models\Customer;
use app\components\CommonUtility;

class ExternalController extends Controller
{
    public $customer;

    public function init()
    {
        $request = Yii::$app->request;
        Yii::info("Received request [$request->method $request->url] "
                  . ("[PARAMS=" . VarDumper::export($request->bodyParams) . "] "), 'service');
        parent::init();
    }

    public function login(bool $needRegister = false)
    {
        $user = $this->loginImpl();
        if ($needRegister && !$user->registered()) {
            return $this->redirect(['customer/create',
                                    'redirectUrl' => Yii::$app->request->absoluteUrl]);
        }
        return $user;
    }
    
    public function loginImpl()
    {
        return Customer::findOne(42);
        
        $request = Yii::$app->request;
        $session = Yii::$app->session;
        $session->open();

        if ($session->has('openID')) {
            $openID = $session['openID'];
            $this->customer = Customer::findOne(['open_id' => $openID]);
            if (!$this->customer) {
                Yii::warning('Expired session with invalid openID=' . $openID);
                $session->remove('openID');
                $this->redirect(Yii::$app->utils
                                ->generateOAuthURL($request->absoluteUrl));
                Yii::$app->end();
            }
            Yii::info('Login as customer=' . $this->customer->id, 'service');
            return $this->customer;
        }

        $code = $request->get('code');
        if (!$code) {
            $this->redirect(Yii::$app->utils
                            ->generateOAuthURL($request->absoluteUrl));
            Yii::$app->end();
        }
        
        $resBody = Yii::$app->utils->fetchOAuthAccessToken($code)['body'];
        $openID = $resBody['openid'];
        $this->customer = Customer::findOne(['open_id' => $openID]);
        if (!$this->customer) {
            $this->customer = new Customer();
            $this->customer->open_id = $openID;
            $this->customer->save();
        }
        $session['openID'] = $openID;
        Yii::info('Login as customer=' . $this->customer->id, 'service');
        return $this->customer;
    }

    public function fetchUserInfo($customer, $code, $state)
    {
        if ($code && $state == 'snsapi_userinfo') {
            $akBody = Yii::$app->utils->fetchOAuthAccessToken($code)['body'];
            $infoBody = Yii::$app->utils->fetchOAuthUserInfo(
                $akBody['access_token'], $akBody['openid'])['body'];
            $customer->nick_name = $infoBody['nickname'];
            $sex = $infoBody['sex'];
            if ($sex == '1') {
                $customer->sexuality = 'male';
            } else if ($sex == '2') {
                $customer->sexuality = 'female';
            } else {
                $customer->sexuality = 'unknown';
            }

            $imgUrl = preg_replace('/0$/', '96', $infoBody['headimgurl']);
            $img = CommonUtility::sendHttpRequest('GET', $imgUrl, 'IMAGE');
            $fname = './images/users/' . $customer->open_id . '.jpg';
            file_put_contents($fname, $img['body']);
            $customer->head_img_path = substr($fname, 1);

            $customer->refresh_token = $akBody['refresh_token'];
            $customer->save();
            return $customer;
        }
        
        // Redirect to fetch user information
        $url = Yii::$app->request->absoluteUrl;
        // Remove former code and state parameters
        $url = preg_replace(['/code=\w+&?/', '/state=\w+&?/'], '', $url);
        $url = preg_replace(['/\?$/', '/&$/'], '', $url);
        $this->redirect(Yii::$app->utils->generateOAuthURL($url, 'snsapi_userinfo'));
        Yii::$app->end();
    }
}