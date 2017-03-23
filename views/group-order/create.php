<?php

/* @var $this yii\web\View */
/* @var $item app\models\Item */
/* @var $user app\models\Customer */
/* @var $order app\models\Order */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$amount = $order->leader_amount ? $order->leader_amount : 1;
$setMaxAmount = $order->max_amount !== null;
$maxAmount = $setMaxAmount ? $order->max_amount : $item->threshold;

$duration = new DateInterval('P' . $item->delivery_duration . 'D');
$arrivalStart = (new DateTime($item->delivery_date_start))->add($duration);
$arrivalEnd = (new DateTime($item->delivery_date_end))->add($duration);
$arrivalStart = $arrivalStart->format('Y-m-d');
$arrivalEnd = $arrivalEnd->format('Y-m-d');

$addressDropdown = Html::dropDownList("delivery-address",
                                      $order->delivery_address_id,
                                      $user->addressOptionArray(),
                                      ['class' => "form-control"]);

$addressTemplate = <<<EOF
<div class="input-group">
    $addressDropdown
    <span class="input-group-btn">
        <button class="btn btn-success" type="submit" name="add-address" value="1">
            <span class="glyphicon glyphicon-plus-sign"></span>
        </button>
    </span>
</div>
{error}
EOF;

$dateTemplate = <<<EOF
<div id='arrival-date' class='input-group date'
     last-date="$order->arrival_date" date-start="$arrivalStart" date-end="$arrivalEnd">
    <input type="text" name="arrival-date" class="form-control">
    <div class="input-group-addon">
         <span class="glyphicon glyphicon-calendar"></span>
    </div>
</div>
{error}
EOF;

$submitUrl = str_replace('create', 'submit', Yii::$app->request->url);
$formatter = \Yii::$app->formatter;
?>

<div class="container store-viewport">
    <?= $this->render('/item/_photo.php', [
        'model' => $item,
    ]) ?>
    
    <div class="row bg-info separator"></div>

    <?php $form = ActiveForm::begin(['id' => "order-form", "action" => $submitUrl]); ?>
    <div class="row">
        <div class="order-label col-xs-3">订购数量</div>
        <div class="col-xs-7">
            <?= Html::textInput('amount', $amount, ['id' => 'amount']) ?>
        </div>
    </div>
    <div class="row">
        <div class="order-label col-xs-3">封顶数量</div>
        <div class="col-xs-7">
            <div class="input-group">
                <?= Html::textInput('max-amount', $maxAmount,
                                    ['id' => 'max-amount',
                                     'disabled' => !$setMaxAmount,
                                     'data-min' => $item->threshold,
                                     'data-max' => $item->amount]) ?>
                <span class="input-group-addon">
                    <?= Html::checkbox("", $setMaxAmount,
                                       ['id' => 'max-amount-flip',]) ?>
                </span>
            </div>
        </div>
        <div id='total' class="order-label col-xs-2">
            最低要求<?= Html::encode($item->threshold) ?>
        </div>
    </div>

    <div class="row bg-info separator"></div>
    
    <div class="row">
        <div class="order-label col-xs-3">送货地址</div>
        <div class="col-xs-9">
            <?php
            $address = $form->field($order, 'delivery_address_id');
            $address->template = $addressTemplate;
            echo $address;
            ?>
        </div>
    </div>
    <div class="row">
        <div class="order-label col-xs-3">到货日期</div>
        <div class="col-xs-9">
            <?php
            $date = $form->field($order, 'arrival_date');
            $date->template = $dateTemplate;
            echo $date;
            ?>
        </div>
    </div>

    <div class="btn-group btn-group-justified">
        <div class="btn-group" role="group">
            <button type="submit" name="submit-order" value="1" class="btn btn-danger">提交订单</button>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
