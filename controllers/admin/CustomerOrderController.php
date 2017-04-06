<?php

namespace app\controllers\admin;

use Yii;
use app\models\CustomerOrder;
use app\models\CustomerOrderSearch;
use app\controllers\admin\InternalController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CustomerOrderController implements the CRUD actions for CustomerOrder model.
 */
class CustomerOrderController extends InternalController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'refund' => ['POST'],
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        $this->login();
        return parent::beforeAction($action);
    }
    
    /**
     * Lists all CustomerOrder models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CustomerOrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single CustomerOrder model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Updates an existing CustomerOrder model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing CustomerOrder model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionRefund($id)
    {
        $order = $this->findModel($id);
        if ($order->status != 'paid') {
            throw new BadRequestHttpException('该订单未支付');
        }
        $ret = Yii::$app->wepay->refund($order);
        if ($ret['return_code'] != 'SUCCESS' || $ret['return_msg'] != "OK") {
            throw new ServerErrorHttpException('退款失败：' . $ret['return_msg']);
        }
        $order->status = 'refunded';
        $order->save();
        return $this->redirect(['index']);
    }
    
    /**
     * Finds the CustomerOrder model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CustomerOrder the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CustomerOrder::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
