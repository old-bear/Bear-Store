<?php

namespace app\controllers;

use Yii;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\filters\VerbFilter;

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
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'create' => ['get', 'post'],
                    'view' => ['get'],
                    'dispatch' => ['post'],
                ],
            ],
        ];
    }

    public function actionCreate($itemID)
    {
        $request = Yii::$app->request;
        $session = Yii::$app->session;
        $session->open();

        $user = $this->login(true);
        $item = Item::findOne($itemID);

        $order = new GroupOrder();
        if ($session->has('groupOrderID')) {
            $orderID = $session['groupOrderID'];
            $order = GroupOrder::findOne(['id' => $orderID, 'status' => 'creating']);
            if (!$order) {
                // Expired session with invalid groupOrderID
                $session->remove('groupOrderID');
                $order = new GroupOrder();
            }
        }
        

        if ($request->isPost) {
            $order->item_id = $itemID;
            $order->leader_id = $user->id;
            $order->leader_amount = $request->post('amount');
            $order->max_amount = $request->post('max-amount');
            $order->setArrivalDate($request->post('arrival-date'),
                                   $item->delivery_duration);
            $order->setDeliveryAddress($request->post('delivery-address', ''));
            $order->status = 'created';

            if ($request->post('submit-order')) {
                $order->scenario = 'submit';
                if ($order->save()) {
                    // Tricky ways to `redirect' to a POST method,
                    // which can use the _POST body passed in
                    return Yii::$app->runAction('customer-order/create',
                                                ['groupOrderID' => $order->id]);
                }
            } else if ($request->post('add-address')) {
                $order->save();
                $session['groupOrderID'] = $order->id;
                $addAddressUrl = Url::to(['customer-address/create',
                                          'redirectUrl' => $request->absoluteUrl]);
                return $this->redirect($addAddressUrl);
            }
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

    public function actionDispatch($id)
    {
        $request = Yii::$app->request;
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;

        $groupOrder = $this->findModel($id);
        $user = $this->login();
        if ($groupOrder->leader_id != $user->id) {
            throw new ForbiddenHttpException('只有团长才能扫码!');
        }

        $qrcodeUrl = $request->post('qrcode-url');
        $customer = Customer::findOne(['qrcode_url' => $qrcodeUrl]);
        if (!$customer) {
            throw new NotFoundHttpException('没有找到该用户');
        }

        $orders = $groupOrder->getCustomerOrder($customer->id);
        if (!$orders) {
            throw new NotFoundHttpException('该用户不在此订单');
        }

        foreach ($orders as $order) {
            $order->status = 'delivered';
            $order->save();
        }
            
        if ($groupOrder->isCompleted()) {
            $groupOrder->status = 'completed';
            $groupOrder->save();
        }
        return ['errMsg' => 'Success'];
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
        $model = GroupOrder::findOne($id);
        if ($model && $model->status != 'creating') {
            return $model;
        } else {
            throw new NotFoundHttpException('抱歉，该订单不存在');
        }
    }
}
