<?php

namespace app\controllers;

use Yii;
use app\models\Customer;
use app\models\CustomerAddress;
use app\models\CustomerOrder;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class ProfileController extends ExternalController
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
                    'index' => ['get'],
                    'order-management' => ['get'],
                    'address-management' => ['get'],
                    'favorite-management' => ['get'],
                ],
            ],
        ];
    }
    
    public function actionIndex() 
    {
        $user = $this->login(true);
        return $this->render('index', ['user' => $user]);
    }
  
    public function actionAddressManagement()
    {
        $user = $this->login(true);
        return $this->render('address', [
            'user' => $user,
            'addresses' => $user->addresses,
        ]);
    }
  
    public function actionOrderManagement()
    {
        $user = $this->login(true);
        $orders = [];
        foreach ($user->getCustomerOrders()
                 ->orderBy('last_modified_time DESC')->all() as $order) {
            $group = $order->groupOrder;
            if (!isset($orders[$group->id])) {
                $orders[$group->id] = $group;
            }
        }
      
        return $this->render('orders', [
            'user' => $user,
            'orders' => $orders,
        ]);
    }
  
    public function actionFavoriteManagement()
    {
        $user = $this->login(true);
        $favs = $user->favorites;
        return $this->render('favorite', [
            'user' => $user,
            'favorites' => $user->favorites,
        ]);
    }
  
}
