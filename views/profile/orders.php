<?php

/* @var $this yii\web\View */
/* @var $orders[] app\models\GroupOrder */

use yii\helpers\Html;
use yii\helpers\Url;
use app\assets\WeAsset;

$allStatus = ['all', 'created', 'delivering', 'delivered', 'completed'];

WeAsset::register($this);

$tabCss = <<<EOF
#return {
    color: navy;
    max-width: 40px;
}

.container {
    padding-top: 55px;
    padding-bottom: 10px;
    background-color: #eeeeee;
}

.weui-cell__hd {
    margin-right: 10px;
}

.weui-panel__bd a {
    padding-top: 0px;
    padding-bottom: 0px;
}

.weui-panel__bd img {
    width: 50px;
    height: 50px;
}

.weui-panel__hd .weui-cell {
    padding: 0px;
}

.weui-panel__ft .weui-cell {
    font-size: 11px;
}

.weui-media-box__bd .weui-cell {
    padding-right: 0px;
}

.weui-cell {
    font-size: 13px;
}
EOF;
$this->registerCss($tabCss);

$tabJs = <<<EOF
$('.weui-navbar__item').click(function() {
    var id = $(this).attr('status');
    $(this).addClass('weui-bar__item_on')
           .siblings('.weui-bar__item_on').removeClass('weui-bar__item_on');
    $('.container').hide();
    $('#' + id).show();
});

$('.container').hide();
$('#all').show();
EOF;
$this->registerJs($tabJs);

?>

<div class="store-viewport">
    <div class="weui-tab">
        <div class="weui-navbar">
            <a href="/profile/index" id="return" class="weui-navbar__item">
                <span class="glyphicon glyphicon-arrow-left"></span>
            </a>
            <div class="weui-navbar__item" status="all">全部</div>
            <div class="weui-navbar__item" status="created">拼团中</div>
            <div class="weui-navbar__item" status="delivering">已成团</div>
            <div class="weui-navbar__item" status="delivered">已到货</div>
            <div class="weui-navbar__item" status="completed">已完成</div>
        </div>
        <?php foreach ($allStatus as $status): ?>
            <?= Html::beginTag('div', ['id' => $status, 'class' => "container"]) ?>
            <?php foreach ($orders as $order): ?>
                <?php if ($status == 'all' || $order->status == $status): ?>
                    <?= $this->render('_order', ['user' => $user, 'order' => $order]) ?>
                <?php endif; ?>
            <?php endforeach; ?>
            <?= Html::endTag('div') ?>
        <?php endforeach; ?>
    </div>  
</div>
