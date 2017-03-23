<?php

/* @var $this yii\web\View */
/* @var $model app\models\Item */
/* @var $order app\models\GroupOrder */
/* @var $user app\models\Customer */

use yii\helpers\Url;
use yii\helpers\Html;

use yii\web\View;

$orderCreateURL = Url::to(['group-order/create', 'itemID' => $model->id], true);
$orderCreateURL = Yii::$app->utils->generateOAuthURL($orderCreateURL);

$joinOrderURL = Url::to(['customer-order/create', 'orderID' => $order->id], true);
$joinOrderURL = Yii::$app->utils->generateOAuthURL($joinOrderURL);

?>

<div id="item-navbar" class="row">
    <a id="customer-service" class="col-xs-2" href="javascript:;">
        <span class="text-center">客服</span>
    </a>
    <a id="share" class="col-xs-2" href="javascript:;">
        <span class="text-center">分享</span>
    </a>
    <a id="favorite-order" class="col-xs-2" href="javascript:;">
        <span class="text-center">收藏</span>
    </a>
  
    <?php if (strcmp($order->status, 'creating') == 0): ?>
        <?= Html::a('开团啦', $orderCreateURL, ["id" => "create-order",
                                                "class" => "text-center col-xs-3"]) ?>
        <?= Html::a('立即参团', $joinOrderURL, ["id" => "join-order",
                                                "class" => "text-center col-xs-3"]) ?>
  
    <?php elseif (strcmp($order->status, 'created') == 0): ?>
        <?= Html::a('待发货', '#', ["id" => "create-order",
                                    "class" => "text-center col-xs-6"]) ?>
  
    <?php elseif (strcmp($order->status, 'delivering') == 0): ?>
        <?= Html::a('待收货', '#', ["id" => "create-order",
                                    "class" => "text-center col-xs-6"]) ?>
  
    <?php elseif (strcmp($order->status, 'delivered') == 0): ?>
        <?php if ($order->leader->id == $user->id): ?>
            <?= Html::a('扫一扫', 'javascript:;', ["id" => "scan-qrcode",
                                                   "class" => "text-center col-xs-6"]) ?>
        <?php else: ?>
            <?= Html::a('待收货', '#', ["id" => "create-order",
                                        "class" => "text-center col-xs-6"]) ?>
  
        <?php endif; ?>
  
    <?php elseif (strcmp($order->status, 'completed') == 0): ?>
        <?= Html::a('已完成', '#', ["id" => "create-order",
                                    "class" => "text-center col-xs-6"]) ?>
  
    <?php elseif (strcmp($order->status, 'cancelled') == 0): ?>
        <?= Html::a('已取消', '#', ["id" => "create-order",
                                    "class" => "text-center col-xs-6"]) ?>
  
    <?php endif; ?>
</div>

<?= $this->render('/item/_popup') ?>
