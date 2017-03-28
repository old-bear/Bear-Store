<?php

/* @var $this yii\web\View */
/* @var $model app\models\Item */

use yii\helpers\Html;
use yii\helpers\Url;
use app\assets\CountdownAsset;

$formatter = \Yii::$app->formatter;

$titleCss = <<<EOF
#item-title {
    font-size: 20px;
}

#item-title .price {
    font-size: 25px;
    font-family: sans-serif;
    color: #f20c00;
}

#item-title .price::first-letter {
    font-size: 20px;
}

#item-title .spec {
    font-size: 15px;
    color: grey;
    padding-top: 11px;
}

#item-title .item-label {
    padding-top: 2px;
}

#countdown {
    font-weight: bold;
    font-size: 15px;
    color: #f20c00;
}
EOF;
$this->registerCss($titleCss);

CountdownAsset::register($this);
$countdownJs = <<<EOF
$("#countdown").countdown($("#countdown").attr("date"))
    .on('update.countdown', function(event) {
        var format = '%H:%M:%S';
        if (event.offset.totalDays > 0) {
            format = '%-D天 ' + format;
        }
        $(this).html(event.strftime(format));
    })
    .on('finish.countdown', function(event) {
        $(this).html('>_< 错过开团时间啦');
    });
EOF;
$this->registerJs($countdownJs);

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
