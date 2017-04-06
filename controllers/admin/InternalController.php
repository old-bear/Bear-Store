<?php

namespace app\controllers\admin;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Request;
use yii\web\BadRequestHttpException;
use yii\filters\VerbFilter;
use app\models\LoginForm;

class InternalController extends Controller
{
    public $layout = 'admin';
    
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function login()
    {
        if (!Yii::$app->user->isGuest) {
            return;
        }
        return $this->redirect(['//admin/login',
                                'redirectUrl' => Yii::$app->request->absoluteUrl]);
    }
    
    public function actionIndex()
    {
        return $this->render('//admin/index');
    }

    public function actionLogin($redirectUrl = '/admin')
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect($redirectUrl);
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->redirect($redirectUrl);
        }
        return $this->render('//admin/login', [
            'model' => $model,
        ]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->render('//admin/index');
    }
}
