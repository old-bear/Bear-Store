<?php

/* @var $this yii\web\View */
/* @var $model app\models\Item */

use yii\helpers\Url;
use yii\helpers\Html;

use yii\web\View;

$orderCreateURL = Url::to(['group-order/create', 'itemID' => $model->id], true);
$orderCreateURL = Yii::$app->utils->generateOAuthURL($orderCreateURL);

?>

<div id="item-navbar" class="row">
    <a id="customer-service" class="col-xs-2" href="#">
        <span class="text-center">客服</span>
    </a>
    <a id="share" class="col-xs-2" href="javascript:;">
        <span class="text-center">分享</span>
    </a>
    <a id="favorite-item" class="col-xs-2" href="javascript:;">
        <span class="text-center">收藏</span>
    </a>
    <?= Html::a('开团啦', $orderCreateURL, ["id" => "create-order",
                                            "class" => "text-center col-xs-6"]) ?>
</div>

<?= $this->render('_popup') ?>
