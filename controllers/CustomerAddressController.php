<?php

namespace app\controllers;

use Yii;
use app\models\Area;
use app\models\Customer;
use app\models\CustomerAddress;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CustomerAddressController implements the CRUD actions for CustomerAddress model.
 */
class CustomerAddressController extends ExternalController
{
    public $layout = 'base';
    /**
     * @inheritdoc
     */
    /// public function behaviors()
    /// {
    ///     return [
    ///         'verbs' => [
    ///             'class' => VerbFilter::className(),
    ///             'actions' => [
    ///                 'delete' => ['POST'],
    ///             ],
    ///         ],
    ///     ];
    /// }

    public function actionCreate($redirectUrl = '')
    {
        $user = $this->login();
        $model = new CustomerAddress();
        $post = Yii::$app->request->post();
        if ($post) {
            $model->customer_id = $user->id;
            if ($model->load($post) && $model->save()) {
                return $this->redirect($redirectUrl);
            }
        }
        return $this->render('create', [
            'model' => $model,
            'province' => null,
            'city' => null,
            'district' => null,
        ]);        
    }
  
    public function actionUpdate($id)
    {
        $user = $this->login();
        $model = $this->findModel($id);

        $district = Area::findOne($model->district_id);
        $city = $district->upperLevel;
        $province = $city->upperLevel;

        $post = Yii::$app->request->post();
        if ($post) {
            if ($model->load($post) && $model->save()) {
                return $this->redirect('/profile/address-management');
            }
        }
        return $this->render('create', [
            'model' => $model,
            'province' => $province->id,
            'city' => $city->id,
            'district' => $district->id,
        ]); 
               
    }
  
    public function actionDelete() {
        $user = $this->login();
        
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        $response->formatters[yii\web\Response::FORMAT_JSON] = [
            'class' => 'yii\web\JsonResponseFormatter',
            'prettyPrint' => true,
        ];
      
        $request = Yii::$app->request;
        if ($request->isPost) {
            $id = $request->post('id');
            if ($id && $id != '') {           
                $model = $this->findModel($id);
              
                if ($model->delete()) {
                    $msg = 'Success';
                }  else {
                    $msg = 'Server error: cannot delete model';
                }
                
            } else {
              $msg = 'Invalid address id';
            }
        } else {
            $msg = 'Not a post request';
        }
        return $this->jsonResponseGenerator($msg);
    }

    /**
     * Displays a single CustomerAddressSearch model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        $order = $this->findModel($id);
        return $this->render('/item/view', [
            'model' => $order->item,
            'order' => $order,
        ]);
    }

    /**
     * Finds the CustomerAddressSearch model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return CustomerAddressSearch the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CustomerAddress::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('抱歉，该地址不存在');
        }
    }
}
