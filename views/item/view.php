<?php

/* @var $this yii\web\View */
/* @var $model app\models\Item */
/* @var $order app\models\GroupOrder */

use yii\helpers\Url;
use yii\helpers\Html;
use app\components\WechatJsProxy;

$url = Url::current([], true);
$formatter = \Yii::$app->formatter;

$wejs = new WechatJsProxy(['appID' => Yii::$app->utils->appID,
                           'url' => $url, 'view' => $this]);
$wejs->setShareInfo($model->name . ' - 快来友团拼团吧', $url,
                    Url::to($model->images[0]->photo_path, true), $model->description);
$wejs->commit();

$infoCss = <<<EOF
#item-info {
    line-height: 1.8;
}

#item-info .item-label {
    padding-top: 3px;
}
EOF;
$this->registerCss($infoCss);

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

    <?= $this->render('/group-order/_navbar', [
        'model' => $model,
        'order' => null,
        'user' => null,
    ]) ?>

</div>
