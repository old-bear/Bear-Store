<?php

/* @var $this yii\web\View */

use yii\web\View;

$popupJs = <<<EOF
$('#share').click(function() {
    $('#share-dialog').fadeIn();
});

$('#share-dialog-ok').click(function() {
    $('#share-dialog').fadeOut();
});
EOF;
$this->registerJs($popupJs);

?>

<div style="display: none;" id="favorite-dialog">
    <div class="weui-mask"></div>
    <div class="weui-dialog">
        <div class="weui-dialog__bd">已加入收藏</div>
        <div class="weui-dialog__ft">
            <a href="javascript:;" class="weui-dialog__btn weui-dialog__btn_primary" id="favorite-dialog-ok">确定</a>
        </div>
    </div>
</div>

<div style="display: none;" id="customer-service-dialog">
    <div class="weui-mask"></div>
    <div class="weui-dialog">
        <div class="weui-dialog__bd">How to contact customer service</div>
        <div class="weui-dialog__ft">
            <a href="javascript:;" class="weui-dialog__btn weui-dialog__btn_primary" id="customer-service-dialog-ok">确定</a>
        </div>
    </div>
</div>
