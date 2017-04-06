<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\CustomerOrder */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Customer Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-order-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'item_id',
            'customer_id',
            'group_order_id',
            'create_time:datetime',
            'amount',
            'price:currency',
            'prepay_id',
            'transaction_id',
            'status',
            'expire_time:datetime',
            'last_modified_time:datetime',
        ],
    ]) ?>

</div>
