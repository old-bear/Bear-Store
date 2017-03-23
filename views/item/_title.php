<?php

/* @var $this yii\web\View */
/* @var $model app\models\Item */

use yii\helpers\Html;
use yii\helpers\Url;

$formatter = \Yii::$app->formatter;

?>

<div id="item-title">
    <div class="row">
        <div class="col-xs-12 title">
            <?= Html::encode($model->name) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-4 price">
            <?= $formatter->asCurrency($model->price) ?>
        </div>
        <div class="col-xs-8 spec">
            <?= Html::encode($model->specification) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-4 item-label">离开团还有</div>
        <?= Html::tag('div', '', ["id" => "countdown",
                                  "class" => "col-xs-8",
                                  "date" => $model->due_date]) ?>
    </div>
</div>
