<?php

namespace app\controllers;

use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

use app\models\Item;
use app\models\Customer;
use app\models\GroupOrder;
use app\models\AccessToken;
use app\models\JsapiTicket;


/**
 * GroupOrderController implements the CRUD actions for GroupOrder model.
 */
class GroupOrderController extends ExternalController
{
    public $layout = 'base';

    /**
     * @inheritdoc
     */
    /// public function behaviors()
    /// {
    ///     return [
    ///     ];
    /// }

    public function actionCreate($itemID)
    {
        $request = Yii::$app->request;
      
        $item = Item::findOne($itemID);
        $user = $this->login();
        $order = $this->_fetchLastOrder();
      
        if (!$user->phone) {
            // MUST register (have phone) to create group order
            return $this->redirect(['customer/create', 
                                    'redirectUrl' => $request->absoluteUrl]);
        }

        return $this->render('/group-order/create', [
            'item' => $item,
            'user' => $user,
            'order' => $order,
        ]);
    }

    public function actionSubmit($itemID)
    {
        $request = Yii::$app->request;
        $session = Yii::$app->session;

        $item = Item::findOne($itemID);
        $user = $this->login();
        $order = $this->_fetchLastOrder();
        
        $order->item_id = $itemID;
        $order->leader_id = $user->id;
        $order->leader_amount = $request->post('amount');
        $order->max_amount = $request->post('max-amount');
        $order->setArrivalDate($request->post('arrival-date'),
                               $item->delivery_duration);
        $order->setDeliveryAddress($request->post('delivery-address', ''));
        $order->status = 'creating';
        $saved = $order->save();

        if ($request->post('submit-order')) {
            $order->scenario = 'submit';
            if ($saved) {
                return $this->redirect(['customer-order/create',
                                       'orderID' => $order->id]);
            }
            
        } else if ($request->post('add-address')) {
            $session->open();
            $session['groupOrderID'] = $order->id;
            $session->close();
            $redirectUrl = Url::to(['group-order/create', 'itemID' => $itemID]);
            return $this->redirect(['customer-address/create',
                                    'redirectUrl' => $redirectUrl]);
        }
        
        return $this->render('/group-order/create', [
            'item' => $item,
            'user' => $user,
            'order' => $order,
        ]);
    }

    /**
     * Displays a single GroupOrderSearch model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        $order = $this->findModel($id);
        $user = $this->login();
      
        $ak = AccessToken::getAccessToken();
        $ticket = JsapiTicket::getJsapiTicket();
        $signPackage = Yii::$app->utils->getSignPackage($ak, $ticket);
      
        return $this->render('view', [
            'model' => $order->item,
            'order' => $order,
            'user' => $user,
            'signPackage' => $signPackage,
        ]);
    }
  
    
    public function actionScanQrcode()
    {
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
      
        $request = Yii::$app->request;
        $msg = '';
        if ($request->isPost) {
            $id = $request->post('id');
            $qrcode_url = $request->post('qrcode_url');
            
            if ($id && $qrcode_url && $qrcode_url != '') {           
                $order = $this->findModel($id);
                if ($order->hasMember($qrcode_url) == 1) {
                    $customer = $order->fetchCustomer($qrcode_url);
                    $customerOrders = $customer->fetchCustomerOrder($id);
                    foreach( $customerOrders as $customerOrder ) {
                        $customerOrder->status = 'delivered';
                        $customerOrder->save();
                    }
                  
                    if ($order->isCompleted()) {
                        $order->status = 'completed';
                        $order->save();
                    }
                    $msg = 'Success';
                } else {
                    $msg = 'Customer is not a member of this group order';
                }
              
            } else {
              $msg = 'Invalid order id or invalid qr code url';
            }
        } else {
           $msg = 'Not a post request';
        }
      
        return ['errMsg' => $msg];
    }


    private function _fetchLastOrder()
    {
        $session = Yii::$app->session;
        $session->open();
        if ($session->has('groupOrderID')) {
            $orderID = $session['groupOrderID'];
            $order = GroupOrder::findOne($orderID);
            if ($order) {
                return $order;
            }
            // Expired session with invalid groupOrderID
            $session->remove('groupOrderID');
        }
        return new GroupOrder();
    }
    
    /**
     * Finds the GroupOrderSearch model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return GroupOrderSearch the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = GroupOrder::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('抱歉，该订单不存在');
        }
    }
}
