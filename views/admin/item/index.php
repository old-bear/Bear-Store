<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\ItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Items';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="item-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Item', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            //'category_i',
            //'category_ii',
            'name',
            'price:currency',
            'amount',
            // 'threshold',
            'specification',
            // 'description',
            'due_date:date',

            [
                'attribute' => 'delivery_address_id',
                'content' => function($model, $key, $index, $column) {
                    return $model->deliveryAddress->toString();
                },
                'filter' => false,
                'enableSorting' => false,
            ],

            // 'delivery_duration',
            'delivery_date_start:date',
            'delivery_date_end:date',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
