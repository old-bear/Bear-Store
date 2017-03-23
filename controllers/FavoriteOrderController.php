<?php

namespace app\controllers;

use Yii;
use app\models\GroupOrder;
use app\models\FavoriteOrder;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class FavoriteOrderController extends ExternalController
{
    public $layout = 'base';
    
    public function actionAdd()
    {
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
      
        $request = Yii::$app->request;
        if ($request->isPost) {
            $user = $this->login();
            $order_id = $request->post('group_order_id');
          
            if (!$order_id || $order_id === '') {
                return $this->jsonResponseGenerator('Invalid order ID');
            }
          
            if (($order = GroupOrder::findOne($order_id)) === null) {
                return $this->jsonResponseGenerator('Group order not found');
            } 
          
            if (FavoriteOrder::existRecord($user->id, $order_id)) {
                return $this->jsonResponseGenerator('Success');
            }
          
            $favorite = new FavoriteOrder();
            $favorite->customer_id = $user->id;
            $favorite->group_order_id = $order_id;
            $saved = $favorite->save();
            if ($saved) {
                return $this->jsonResponseGenerator('Success');
            } else {
                return $this->jsonResponseGenerator('Database error');
            }
        } 
  
        return $this->jsonResponseGenerator('HTTP method is not POST');
    }
  
    public function actionDelete()
    {
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
      
        $request = Yii::$app->request;
        if ($request->isPost) {
            $user = $this->login();
            $order_id = $request->post('group_order_id');
          
            if (!$order_id || $order_id === '') {
                return $this->jsonResponseGenerator('Invalid order ID');
            }
          
            if (($item = GroupOrder::findOne($order_id)) === null) {
                return $this->jsonResponseGenerator('Group order not found');
            } 
          
            if (!(FavoriteOrder::existRecord($user->id, $order_id))) {
                return $this->jsonResponseGenerator('Success');
            }
          
            $favorite = $user->fetchFavoriteOrder($order_id);
            $deleted = $favorite->delete();
            if ($deleted) {
                return $this->jsonResponseGenerator('Success');
            } else {
                return $this->jsonResponseGenerator('Database error');
            }
        } 
  
        return $this->jsonResponseGenerator('HTTP method is not POST');
    }

    /**
     * Finds the FavoriteItem model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return FavoriteItem the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = FavoriteOrder::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('抱歉，该收藏不存在');
        }
    }

}
