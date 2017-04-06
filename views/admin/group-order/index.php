<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\GroupOrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Group Orders';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="group-order-index">

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
                'attribute' => 'leader_id',
                'content' => function($model, $key, $index, $column) {
                    return "<a href='/admin/customer/view?id={$model->leader_id}"
                        . "'>{$model->leader_id}</a>";
                }
            ],

            'create_time:datetime',            
            // 'delivery_date',
            'arrival_date:date',
            
            // 'leader_amount',
            // 'max_amount',
            [
                'attribute' => 'delivery_address_id',
                'content' => function($model, $key, $index, $column) {
                    return $model->deliveryAddressString();
                },
                'filter' => false,
                'enableSorting' => false,
            ],
            
            'status',
            // 'last_modified_time',

            [
                'class' => 'yii\grid\ActionColumn',
                'buttons' => [
                    'customer-orders' => function($url, $model, $key) {
                        return Html::a('用户订单', '/admin/customer-order/index?'
                                       . "CustomerOrderSearch%5Bgroup_order_id%5D=$key");
                    },
                ],
                'template' => '{view} {customer-orders}',
            ],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
