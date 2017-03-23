<?php

namespace app\controllers;

use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

use app\models\Item;
use app\models\Customer;
use app\models\CustomerOrder;
use app\models\GroupOrder;
use app\models\AccessToken;
use app\models\JsapiTicket;
use app\components\LoginBehavior;
use app\controllers\ExternalController;


/**
 * GroupOrderController implements the CRUD actions for GroupOrder model.
 */
class CustomerOrderController extends ExternalController
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

    public function actionCreate($orderID, $amount = null)
    {
        $groupOrder = GroupOrder::findOne($orderID);
        $item = $groupOrder->item;
        $user = $this->login(true);

        $customerOrder = $this->_fetchLastOrder();
        $customerOrder->item_id = $item->id;
        $customerOrder->customer_id = $user->id;
        $customerOrder->group_order_id = $groupOrder->id;
        $customerOrder->amount = $amount ? $amount : 1;
        $customerOrder->price = $customerOrder->amount * $item->price;
 
        return $this->render('create', [
            'item' => $item,
            'user' => $user,
            'order' => $customerOrder,
        ]);
    }

    public function actionSubmit($orderID)
    {
        $request = Yii::$app->request;
        $session = Yii::$app->session;

        $groupOrder = GroupOrder::findOne($orderID);
        $item = $groupOrder->item;
        $user = $this->login();

        $customerOrder = $this->_fetchLastOrder();
        $customerOrder->item_id = $item->id;
        $customerOrder->customer_id = $user->id;
        $customerOrder->group_order_id = $groupOrder->id;
        $customerOrder->amount = $request->post('amount');
        $customerOrder->price = $customerOrder->amount * $item->price;
      
        $saved = $customerOrder->save();
      
        $post = $request->post('submit-customer-order');

        if ($post) {
            
            if ($item->amount > $customerOrder->amount && $saved) {
                $item->amount -= $customerOrder->amount;
                $item->save();
                return $this->redirect(['/customer-order/pay',
                                        'id' => $customerOrder->id]);
            } 
        } 
      
        return $this->render('create', [
            'item' => $item,
            'user' => $user,
            'order' => $customerOrder,
        ]);
    }
  
    public function actionPay($id) 
    {
        $request = Yii::$app->request;
        $user_ip = $request->getUserIP();
        
        $order = $this->findModel($id);
      
        $ak = AccessToken::getAccessToken();
        $ticket = JsapiTicket::getJsapiTicket();
        $signPackage = Yii::$app->utils->getSignPackage($ak, $ticket);

        $prepay_id = self::_fetchPrepayId($order, $user_ip);
        $paySignPackage = Yii::$app->utils->getWxPaySignPackage($prepay_id);
        
        return $this->render('pay', [
            'signPackage' => $signPackage,
            'paySignPackage' => $paySignPackage,
            'order' => $order,
        ]);
    }
  
    public function actionRefund()
    {
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
      
        $request = Yii::$app->request;
        if ($request->isPost) {
          
            $id = $request->post('order_id');
            $order = $this->findModel($id);
            if ($order->groupOrder->status === 'cancelled') {
                $postDataXML = Yii::$app->utils->generateRefundData($order);
                $resp = Yii::$app->utils->postRefundRequest($postDataXML);
                $respData = Yii::$app->utils->parseResponseXML($resp);

                if ($respData['result_code'] === 'SUCCESS') {
                    $order->status = 'refunded';
                    $order->save();
                }

                return json_encode($respData);
            } else {
                return $this->jsonResponseGenerator('The group order is confirmed, cannot refund');
            }
        } 
      
        return $this->jsonResponseGenerator('HTTP method is not POST');
    }
  
    public function actionNotify()
    {
        $request = Yii::$app->request;
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_XML;
        
        Yii::trace("[NOTIFY_URL]" . json_encode($request->bodyParams), 'service');
      
        $data = json_decode(json_encode($request->bodyParams), true);
      
        if (strcmp($data['result_code'], 'SUCCESS') == 0) {
            $order = $this->findModel($data['out_trade_no']);
            $order->pay_id = $data['transaction_id'];
            $order->status = 'paid';
            $order->save();
            return Yii::$app->utils->wechatNotifyResponse('SUCCESS', 'OK');
        } else {
            return Yii::$app->utils->wechatNotifyResponse('FAIL', 'Wechat Payment Failed');
        }
        
        return Yii::$app->utils->wechatNotifyResponse('SUCCESS', 'OK');
    }

    /**
     * Displays a single GroupOrderSearch model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        $order = $this->findModel($id);
        return $this->render('view', [
            'model' => $order->item,
            'order' => $order,
            'groupOrder' => $order->groupOrder,
        ]);
    }
  
    public static function _fetchPrepayId($order, $user_ip)
    {
        $prepay_id = $order->pay_id;
        if (!$prepay_id) {
            $prepay_id = self::_refreshPrepayId($order, $user_ip);
        } else {
            $expireTime = new \Datetime($order->expire_time);
            $diff = $expireTime->getTimestamp() - time();
            if ($diff < 900) {
                $prepay_id = self::_refreshPrepayId($order, $user_ip);
            }
        }
      
        return $prepay_id;
    }
  
    public static function _refreshPrepayId($order, $user_ip)
    {
        $customer = $order->customer;
      
        $postDataXML = Yii::$app->utils->generateUnifiedOrderData($order, $user_ip, $customer->open_id);
        $resp = Yii::$app->utils->postUnifiedOrderRequest($postDataXML);
        $respData = Yii::$app->utils->parseResponseXML($resp['body']);

        $order->pay_id = $respData['prepay_id'];
        $order->status = 'created';
        $expireTime = (new \DateTime())->add(new \DateInterval('PT7200S'));
        $order->expire_time = $expireTime->format('Y-m-d H:i:s');
        $order->save();
      
        return $order->pay_id;
    }

    private function _fetchLastOrder()
    {
        $session = Yii::$app->session;
        $session->open();
        if ($session->has('customerOrderID')) {
            $orderID = $session['customerOrderID'];
            $order = CustomerOrder::findOne($orderID);
            if ($order) {
                return $order;
            }
            // Expired session with invalid groupOrderID
            $session->remove('customerOrderID');
        }
        return new CustomerOrder();
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
        if (($model = CustomerOrder::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('抱歉，该订单不存在');
        }
    }
}
