<?php

namespace app\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\filters\VerbFilter;
use app\models\Item;
use app\models\Favorite;

class FavoriteController extends ExternalController
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
                    'create' => ['post'],
                    'delete' => ['delete'],
                ],
            ],
        ];
    }

    public function actionCreate()
    {
        $request = Yii::$app->request;
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
      
        $user = $this->login();
        $favorite = new Favorite();
        $favorite->customer_id = $user->id;
        $favorite->resource_type = $request->post('resource_type');
        $favorite->resource_id = $request->post('resource_id');
        if ($favorite->duplicate()) {
            throw new BadRequestHttpException('Duplicate favorite');
        }
        if (!$favorite->save()) {
            throw new BadRequestHttpException('Invalid favorite');
        }
        return ['id' => $favorite->id];
    }
  
    public function actionDelete($id)
    {
        $request = Yii::$app->request;
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
      
        $user = $this->login();
        $favorite = $this->findModel($id);
        if (!$favorite) {
            throw new NotFoundHttpException("Can't find favorite=" . $id);
        }
        if ($favorite->customer_id != $user->id) {
            throw new ForbiddenHttpException("This favorite=" . $id
                                             . " doesn't belong to user=" . $user->id);
        }
        $favorite->delete();
        return ['errMsg' => 'Success'];
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
        if (($model = Favorite::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('抱歉，该收藏不存在');
        }
    }

}
