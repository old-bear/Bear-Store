<?php

namespace app\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\Cookie;

use app\models\ItemSearch;

class StoreController extends Controller
{
    public $layout = 'base';
  
    public function actionIndex()
    {
        $searchModel = new ItemSearch();
        $query = Yii::$app->request->queryParams;
        $dataProvider = $searchModel->search($query);
        $models = $dataProvider->getModels();
      
        return $this->render('index', [
            'models' => $models,
        ]);
    }
  
    public function actionCart()
    {
        $cookies = Yii::$app->request->cookies;  
      
        $items = [];
        if (isset($cookies['cart'])) {
            $items = json_decode($cookies['cart']->value);
        }
         
        $total = 0;
        foreach ($items as $id => $item) {
            if (($model = ItemSearch::findOne($item->id)) !== null) {
                $item->model = $model;
                $total += $model->price * $item->count;
            } else {
                throw new NotFoundHttpException('抱歉，该商品不存在');
            }
        }
        
        return $this->render('cart', [
            'items' => $items,
            'total' => $total,
        ]);
        
    }

}
