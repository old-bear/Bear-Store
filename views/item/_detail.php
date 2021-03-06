<?php

/* @var $this yii\web\View */
/* @var $model app\models\Item */
/* @var $offset integer */

use yii\helpers\Html;

$col1 = "col-xs-" . $offset;
$col2 = "col-xs-" . (12 - $offset);

$detailCss = <<<EOF
#item-detail p {
    font-size: 13px;
    font-family: simhei;
}

#item-detail .item-label {
    font-size: 12px;
    color: grey;
}
EOF;
$this->registerCss($detailCss);

?>

<div id="item-detail">
    <div class="row">
        <?= Html::tag('div', '详情', ["class" => $col1 . " item-label"]) ?>
        <?= Html::beginTag('div', ["class" => $col2]) ?>
        <?= Html::tag('p', $model->description) ?>
        <?= Html::endTag('div') ?>
    </div>
</div>
