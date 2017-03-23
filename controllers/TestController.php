<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Request;
use yii\web\BadRequestHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use app\models\AccessToken;
use app\models\Order;
use app\models\Customer;
use app\models\OrderCustomer;
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
        return Yii::getAlias('@yii/gii');
    }

    public function actionTest1()
    {
        $user = $this->login(true);
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        $response->formatters[yii\web\Response::FORMAT_JSON] = [
            'class' => 'yii\web\JsonResponseFormatter',
            'prettyPrint' => true,
        ];
        return ['ret' => ''];
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
