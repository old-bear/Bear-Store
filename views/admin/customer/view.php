<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Customer */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Customers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'open_id',
            //'captcha',
            'subscribe_time:datetime',
            //'refresh_token',
            'nick_name',
            'sexuality',
            [
                'attribute' => 'head_img_path',
                'value' => $model->head_img_path,
                'format' => 'image',
            ],
            'balance:currency',
            'bonus_points',
            'phone',
            'email:email',
            [
                'attribute' => 'qrcode_ticket',
                'value' => 'https://mp.weixin.qq.com/cgi-bin/'
                           . "showqrcode?ticket={$model->qrcode_ticket}",
                'format' => 'url',
            ],
            //'qrcode_expire_time',
            //'qrcode_url:url',
        ],
    ]) ?>

</div>
