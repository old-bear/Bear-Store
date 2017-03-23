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
  
    
    public function actionIndex() 
    {
        $user = $this->login();
        
        return $this->render('index',[
            'user' => $user,
        ]);
    }
  
    public function actionAddressManagement()
    {
        $user = $this->login();
      
        return $this->render('address',[
            'user' => $user,
            'addresses' => $user->addresses,
        ]);
    }
  
    public function actionOrderManagement($status = null)
    {
        $user = $this->login();
      
        $orders = null;
        if (!$status) {
            $orders = $user->orders;
        } else {
            $orders = $user->fetchOrders($status);
        }
      
        return $this->render('orders',[
            'user' => $user,
            'orders' => $orders,
            'status' => $status,
        ]);
    }
  
    public function actionFavoriteItem()
    {
        $user = $this->login();
        $items = $user->favoriteItems;
        return $this->render('favorite_item', [
            'items' => $items,
        ]);
    }
  
    public function actionFavoriteOrder()
    {
        $user = $this->login();
        $orders = $user->favoriteOrders;
        return $this->render('favorite_order', [
            'user' => $user,
            'orders' => $orders,
        ]);
    }

}
