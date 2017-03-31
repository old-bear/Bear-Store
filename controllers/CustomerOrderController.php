<?php

namespace app\controllers;

use Yii;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\ConflictHttpException;
use yii\web\ServerErrorHttpException;
use yii\web\UnauthorizedHttpException;
use yii\filters\VerbFilter;

use app\models\Item;
use app\models\Customer;
use app\models\CustomerOrder;
use app\models\GroupOrder;
use app\models\AccessToken;
use app\models\JsapiTicket;
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
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'create' => ['get', 'post'],   // get for the redirection
                    'notify' => ['post'],
                ],
            ],
        ];
    }

    public function actionCreate($groupOrderID)
    {
        $request = Yii::$app->request;
        $user = $this->login(true);
        $amount = $request->post('amount');
        if (!$amount) {
            throw new BadRequestHttpException('请指定数量');
        }
        
        $groupOrder = GroupOrder::findOne($groupOrderID);
        if ($groupOrder->max_amount &&
            $groupOrder->max_amount
            < $groupOrder->getCustomerOrders()->sum('amount') + $amount) {
            throw new ConflictHttpException('抱歉，该团上限已满');
        }

        $item = $groupOrder->item;
        if ($amount > $item->amount) {
            throw new ConflictHttpException('抱歉，该商品已断货');
        }
        $item->amount -= $amount;
        $item->save();
        
        $customerOrder = new CustomerOrder();
        $customerOrder->item_id = $item->id;
        $customerOrder->customer_id = $user->id;
        $customerOrder->group_order_id = $groupOrder->id;
        $customerOrder->amount = $amount;
        $customerOrder->price = $customerOrder->amount * $item->price;
        $customerOrder->status = 'creating';
        $customerOrder->save();      // To generate id

        $pay = Yii::$app->wepay->unifiedOrder($customerOrder,
                                              $request->getUserIP(), $user->open_id);
        $customerOrder->prepay_id = $pay['prepay_id'];
        $expireTime = (new \DateTime())->add(new \DateInterval('PT7200S'));
        $customerOrder->expire_time = $expireTime->format('Y-m-d H:i:s');
        $customerOrder->status = 'created';
        $customerOrder->save();
            
        return $this->render('create', [
            'order' => $customerOrder,
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
        $response->formatters[yii\web\Response::FORMAT_XML] = [
            'class' => 'yii\web\XmlResponseFormatter',
            'rootTag' => 'xml',
        ];
        
        $body = $request->post();
        $sign = $body['sign'];
        unset($body['sign']);
        if (!Yii::$app->wepay->verifySign($body, $sign)) {
            throw new UnauthorizedHttpException('你是谁！');
        }

        if ($body['return_code'] != 'SUCCESS' || $body['result_code'] != 'SUCCESS') {
            throw new ServerErrorHttpException('支付失败：' . $body['return_msg']);
        }
        
        $order = $this->findModel($body['out_trade_no']);
        $order->transaction_id = $body['transaction_id'];
        $order->status = 'paid';
        $order->save();
        return ['return_code' => 'SUCCESS', 'return_msg' => 'OK'];
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
