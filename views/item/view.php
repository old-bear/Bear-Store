<?php

/* @var $this yii\web\View */
/* @var $model app\models\Item */
/* @var $order app\models\GroupOrder */

use yii\helpers\Html;

$formatter = \Yii::$app->formatter;

?>

<div class="container store-viewport">
    <?= $this->render('_photo', [
        'model' => $model,
    ]) ?>

    <div class="row separator"></div>

    <?= $this->render('_title', [
        'model' => $model,
    ]) ?>

    <div class="row separator"></div>

    <div id="item-info">
        <div class="row">
            <div class="col-xs-3 item-label">成团要求</div>
            <div class="col-xs-9">
                最低<?= Html::tag('span', $model->threshold, ["id" => "threshold"]) ?>件起
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3 item-label">截止时间</div>
            <div class="col-xs-9">
                <?= $formatter->asDate($model->due_date) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3 item-label">送货时间</div>
            <div class="col-xs-9">
                <?= $formatter->asDate($model->delivery_date_start) . ' - '
                  . $formatter->asDate($model->delivery_date_end); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3 item-label">送货范围</div>
            <div class="col-xs-9">
                <?= Html::encode($model->deliveryAddress->toString()) ?>
            </div>
        </div>
    </div>

    <div class="row separator"></div>

    <?= $this->render('_detail', [
        'model' => $model,
        'offset' => 3,
    ]) ?>

    <?= $this->render('_navbar', [
        'model' => $model,
    ]) ?>

</div>
