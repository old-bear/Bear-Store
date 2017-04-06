<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\CustomerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Customers';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Customer', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            'open_id',
            //'captcha',
            //'subscribe_time:datetime',
            //'refresh_token',
            'nick_name',
            'sexuality',
            // 'head_img_path',
            'balance:currency',
            // 'bonus_points',
            'phone',
            'email:email',
            // 'qrcode_ticket',
            // 'qrcode_expire_time',
            // 'qrcode_url:url',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {refund}',
            ],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
