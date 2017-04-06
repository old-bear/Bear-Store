<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\GroupOrder */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Group Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="group-order-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'item_id',
            'leader_id',
            'create_time:datetime',
            'delivery_date:date',
            'arrival_date:date',
            //'leader_amount',
            'max_amount',
            [
                'attribute' => 'delivery_address_id',
                'value' => $model->deliveryAddressString(),
            ],
            'status',
            'last_modified_time:datetime',
        ],
    ]) ?>

</div>
