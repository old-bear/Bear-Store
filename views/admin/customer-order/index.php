<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\CustomerOrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Customer Orders';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-order-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            [
                'attribute' => 'item_id',
                'content' => function($model, $key, $index, $column) {
                    return "<a href='/admin/item/view?id={$model->item_id}"
                        . "'>{$model->item_id}</a>";
                }
            ],
            
            [
                'attribute' => 'customer_id',
                'content' => function($model, $key, $index, $column) {
                    return "<a href='/admin/customer/view?id={$model->customer_id}"
                        . "'>{$model->customer_id}</a>";
                }
            ],

            [
                'attribute' => 'group_order_id',
                'content' => function($model, $key, $index, $column) {
                    return "<a href='/admin/group-order/view?id={$model->group_order_id}"
                        . "'>{$model->group_order_id}</a>";
                }
            ],

            'create_time:datetime',
            'amount',
            'price:currency',
            
            //'prepay_id',
            'transaction_id',
            'status',
            //'expire_time',
            //'last_modified_time',

            [
                'class' => 'yii\grid\ActionColumn',
                'buttons' => [
                    'refund' => function($url, $model, $key) {
                           return Html::a('退款', ['refund', 'id' => $key],
                                          ['data' => [
                                              'confirm' => '确认退款？',
                                              'method' => 'post',
                                          ]]);
                    }
                ],
                'template' => '{view} {refund}',
            ],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
