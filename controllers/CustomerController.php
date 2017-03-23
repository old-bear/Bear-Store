<?php

namespace app\controllers;

use Yii;
use app\models\Customer;
use app\models\CustomerAddress;
use app\models\CustomerOrder;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;


/**
 * CustomerController implements the CRUD actions for Customer model.
 */
class CustomerController extends ExternalController
{
    public $layout = 'base';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'create' => ['get', 'post'],
                    'send-verification' => ['post'],
                ],
            ],
        ];
    }

    public function actionCreate($redirectUrl = '/profile/index')
    {
        $request = Yii::$app->request;
        $user = $this->login();
        if (empty($user->nick_name)) {
            // The first visit for this customer
            $this->fetchUserInfo($user, $request->get('code'), $request->get('state'));
        }
        
        $user->scenario = "register";
        if ($request->isPost && $user->load($request->post()) && $user->save()) {
            return $this->redirect($redirectUrl);
        }
        return $this->render('create', ['model' => $user]);        
    }

    /**
     * Push verification code to customer's phone.
     * Handle post request only.
     */
    public function actionSendVerification()
    {
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
      
        $request = Yii::$app->request;
        $phone = $request->post('phone');
        $verification = (string) mt_rand(100000, 999999);
          
        $user = $this->login();
        $user->captcha = $verification;
        $user->save();
          
        $msg = '';
        if ($phone != '') {
            $ret = Yii::$app->sms->sendVerificationCode($phone, $verification);
            if ($ret['code'] == 1000) {
                $msg = 'Success';
            } else {
                $msg = 'SMS service error: '. $ret['message'];
            }
        } else {
            $msg = 'Invalid phone number';
        }
        return ['errMsg' => $msg];
    }
}
