<?php

namespace app\controllers;

use Yii;
use app\models\Item;
use app\models\FavoriteItem;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class FavoriteItemController extends ExternalController
{
    public $layout = 'base';
    
    public function actionAdd()
    {
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
      
        $request = Yii::$app->request;
        if ($request->isPost) {
            $user = $this->login();
            $item_id = $request->post('item_id');
          
            if (!$item_id || $item_id === '') {
                return $this->jsonResponseGenerator('Invalid item ID');
            }
          
            if (($item = Item::findOne($item_id)) === null) {
                return $this->jsonResponseGenerator('Item not found');
            } 
          
            if (FavoriteItem::existRecord($user->id, $item_id)) {
                return $this->jsonResponseGenerator('Success');
            }
          
            $favorite = new FavoriteItem();
            $favorite->customer_id = $user->id;
            $favorite->item_id = $item_id;
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
            $item_id = $request->post('item_id');
          
            if (!$item_id || $item_id === '') {
                return $this->jsonResponseGenerator('Invalid item ID');
            }
          
            if (($item = Item::findOne($item_id)) === null) {
                return $this->jsonResponseGenerator('Item not found');
            } 
          
            if (!(FavoriteItem::existRecord($user->id, $item_id))) {
                return $this->jsonResponseGenerator('Success');
            }
          
            $favorite = $user->fetchFavoriteItem($item_id);
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
        if (($model = FavoriteItem::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('抱歉，该收藏不存在');
        }
    }

}
