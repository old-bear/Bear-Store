<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Request;
use yii\web\BadRequestHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\helpers\VarDumper;
use app\models\AccessToken;
use app\models\Customer;
use app\models\Item;
use app\models\GroupOrder;
use app\models\CustomerOrder;
use app\models\CustomerAddress;
use app\components\CommonUtility;
use app\components\InternalException;

class TestController extends ExternalController
{
    /// public function behaviors()
    /// {
    ///     return [
    ///         'access' => [
    ///             'class' => AccessControl::className(),
    ///             'only' => ['logout'],
    ///             'rules' => [
    ///                 [
    ///                     'actions' => ['logout'],
    ///                     'allow' => true,
    ///                     'roles' => ['@'],
    ///                 ],
    ///             ],
    ///         ],
    ///         'verbs' => [
    ///             'class' => VerbFilter::className(),
    ///             'actions' => [
    ///                 'logout' => ['post'],
    ///             ],
    ///         ],
    ///     ];
    /// }

    /// public function actions()
    /// {
    ///     return [
    ///         'error' => [
    ///             'class' => 'yii\web\ErrorAction',
    ///         ],
    ///         'captcha' => [
    ///             'class' => 'yii\captcha\CaptchaAction',
    ///             'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
    ///         ],
    ///     ];
    /// }

    public function actionTest()
    {
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        $response->formatters[yii\web\Response::FORMAT_JSON] = [
            'class' => 'yii\web\JsonResponseFormatter',
            'prettyPrint' => true,
        ];
        $body = [    'appid' => 'wx19722aca1cab5994',
                     'bank_type' => 'CMB_CREDIT',
                     'cash_fee' => '100',
                     'fee_type' => 'CNY',
                     'is_subscribe' => 'Y',
                     'mch_id' => '1417391302',
                     'nonce_str' => 'g4bTTTqxJj0KEOdk',
                     'openid' => 'ohdHXv7Q9Qir-VZx9RaGcH13kp-g',
                     'out_trade_no' => '87',
                     'result_code' => 'SUCCESS',
                     'return_code' => 'SUCCESS',
                     'time_end' => '20170328210835',
                     'total_fee' => '100',
                     'trade_type' => 'JSAPI',
                     'transaction_id' => '4003532001201703284987221573',
        ];

        $co = CustomerOrder::findOne(141);
        return Yii::$app->wepay->refund($co);
    }

    public function actionTest1()
    {
        $response = Yii::$app->response;

        $order = GroupOrder::findOne(68);
        $item = Item::findOne(1);

        $arr = ['a'=>1, 'b'=>['c'=>'d']];
        $xml = CommonUtility::array2xml($arr);

        return Yii::$app->wepay->apiclientCert;
    }

    public function actionCreate()
    {
        $ak = AccessToken::getAccessToken();
        $ret = Yii::$app->utils->fetchUserInfo($ak, 'ooHIqwy_YsUG39twxV9gXGYg3VYM');
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        $response->formatters[yii\web\Response::FORMAT_JSON] = [
            'class' => 'yii\web\JsonResponseFormatter',
            'prettyPrint' => true,
        ];
        return ['ret' => $ret];
    }    

    public function actionSearch($code)
    {
        $ret = Yii::$app->utils->fetchOAuthAccessToken($code);
        $body = $ret['body'];

        /// $ak = $body['access_token'];
        /// $openID = $body['openid'];
        /// $ret = Yii::$app->utils->fetchOAuthUserInfo($ak, $openID);
        
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        $response->formatters[yii\web\Response::FORMAT_JSON] = [
            'class' => 'yii\web\JsonResponseFormatter',
            'prettyPrint' => true,
        ];
        return ['ret' => $ret];
    }    

}
