<?php

/* @var $this yii\web\View */
/* @var $model app\models\Item */
/* @var $order app\models\CustomerOrder */

use yii\helpers\Html;

$formatter = \Yii::$app->formatter;

?>

<div class="container store-viewport">
    <?= $this->render('/item/_photo', [
        'model' => $model,
    ]) ?>

    <div class="row separator"></div>

    <?= $this->render('/item/_title', [
        'model' => $model,
    ]) ?>

    <div class="row separator"></div>

    <div id="item-order">
        <div id="customer-order-amount" class="row">
            <div class="col-xs-2 item-label">数量</div>
            <div class="col-xs-5">
                <?= Html::encode($order->amount) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-2 item-label">预计</div>
            <div class="col-xs-10"><?= $formatter->asDate($groupOrder->arrival_date) ?></div>
        </div>
        <div class="row">
            <div class="col-xs-2 item-label">送至</div>
            <div class="col-xs-10"><?= Html::encode($groupOrder->deliveryAddressString()) ?></div>
        </div>
    </div>

    <div class="row separator"></div>
    
    <div id="group-detail">
        <div class="row">
            <div class="col-xs-2 item-label">成员</div>
            <div class="col-xs-3">
                <?= Html::img($groupOrder->leader->head_img_path, ['id' => "head-img"]) ?>
            </div>
            <div class="col-xs-4">
                <div class="row"><?= Html::encode($groupOrder->leader->nick_name) ?></div>
                <hr class="row"></hr>
                <div class="row"><?= Html::encode($groupOrder->leader->phone) ?></div>
            </div>
            <div class="col-xs-3">
                <img id="leader-img" src="/images/leader.png" />
            </div>
        </div>
    </div>

</div>
