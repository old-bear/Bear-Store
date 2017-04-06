<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Item */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="item-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'category_i',
            'category_ii',
            'name',
            'price:currency',
            'amount',
            'threshold',
            'specification',
            'description',
            'due_date:date',
            [
                'attribute' => 'delivery_address_id',
                'value' => $model->deliveryAddress->toString(),
            ],
            'delivery_duration',
            'delivery_date_start:date',
            'delivery_date_end:date',
            [
                'label' => '图片',
                'value' => $model->images[0]->photo_path,
                'format' => 'image',
            ],
        ],
    ]) ?>

</div>
