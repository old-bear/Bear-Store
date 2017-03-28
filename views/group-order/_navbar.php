<?php

/* @var $this yii\web\View */
/* @var $model app\models\Item */
/* @var $order app\models\GroupOrder */
/* @var $user app\models\Customer */

use yii\helpers\Url;
use yii\helpers\Html;

use yii\web\View;

$navbtnJs = <<<EOF
$('#join-order').click(function() {
    $('#join-form').submit();
});
EOF;
$this->registerJs($navbtnJs);

$navbtnColor = '#ff7500';
if ($order) {
    if ($order->status == 'delivered' || $order->status == 'completed') {
        $navbtnColor = '#00bc12';
    } else if ($order->status == 'cancelled') {
        $navbtnColor = '#c2ccd0';
    }
}
$navbarCss = <<<EOF
#item-navbar {
    width: 100%;
    max-width: 640px;
    height: 55px;
    z-index: 100;
    position: fixed;
    bottom: 0px;
    border-top: 1px solid #dddddd;
}

#item-navbar a {
    display: inline-block;
    height: 55px;
    padding-left: 5px;
    padding-right: 5px;
    background-repeat: no-repeat;
    background-position: center 25%;
    background-size: 1.8em;
    background-color: rgba(245, 245, 245, 0.8);
    border-right: 1px solid #dddddd;
}

#item-navbar span {
    display: inline-block;
    width: 100%;
    padding-top: 35px;
    color: grey;
    font-size: 12px;
    font-family: sans-serif;
}

#share {
    background-image: url("/images/glyphicons/glyphicons-309-share-alt.png");
}

#favorite {
    background-image: url("/images/glyphicons/glyphicons-20-heart-empty.png");
}

#profile {
    background-image: url("/images/glyphicons/glyphicons-4-user.png");
}

#item-navbar .navbtn {
    color: white;
    font-size: 15px;
    padding-top: 18px;
    background-color: $navbtnColor;
}

#item-navbar #join-order {
    background-color: #ff3300;
}
EOF;
$this->registerCss($navbarCss);

$navbtnClass = "text-center navbtn";
if ($order && $order->status == 'created') {
    $navbtnClass .= ' col-xs-3';
} else {
    $navbtnClass .= ' col-xs-6';
}
if ($order && ($order->status == 'delivering'
               || $order->status == 'cancelled' || $order->status == 'completed'
               || ($order->status == 'delivered' && $order->leader->id != $user->id))) {
    $navbtnClass .= ' disabled';
}

$orderCreateURL = Url::to(['group-order/create', 'itemID' => $model->id], true);

?>

<div id="item-navbar" class="row">
    <a id="share" class="col-xs-2" href="javascript:;">
        <span class="text-center">分享</span>
    </a>
    <a id="favorite" class="col-xs-2" href="javascript:;">
        <span class="text-center">收藏</span>
    </a>
    <a id="profile" class="col-xs-2" href="/profile/index">
        <span class="text-center">我的友团</span>
    </a>

    <?php if (!$order): ?>
        <?= Html::a('开团啦', $orderCreateURL, ["class" => $navbtnClass]) ?>
    
    <?php elseif (strcmp($order->status, 'created') == 0): ?>
        <?= Html::a('单独开团', $orderCreateURL, ["class" => $navbtnClass]) ?>
        <?= Html::a('提交订单', 'javascript:;', ["id" => "join-order",
                                                 "class" => $navbtnClass]) ?>
  
    <?php elseif (strcmp($order->status, 'delivering') == 0): ?>
        <?= Html::a('已成团，待收货', '#', ["class" => $navbtnClass]) ?>
  
    <?php elseif (strcmp($order->status, 'delivered') == 0): ?>
        <?php if ($order->leader->id == $user->id): ?>
            <?= Html::a('扫一扫发货', 'javascript:;', ['id' => 'scan-qrcode',
                                                       "class" => $navbtnClass]) ?>
        <?php elseif ($order->customerOrderCompleted($user->id)): ?>
            <?= Html::a('已完成 ^_^', '#', ["class" => $navbtnClass]) ?>
        <?php else: ?>
            <?= Html::a('已到货，找团长', '#', ["class" => $navbtnClass]) ?>
        <?php endif; ?>
  
    <?php elseif (strcmp($order->status, 'completed') == 0): ?>
        <?= Html::a('已完成 ^_^', '#', ["class" => $navbtnClass]) ?>
  
    <?php elseif (strcmp($order->status, 'cancelled') == 0): ?>
        <?= Html::a('已取消 >_<', '#', ["id" => "order-cancel", "class" => $navbtnClass]) ?>
  
    <?php endif; ?>
</div>

<?= $this->render('/item/_popup') ?>
