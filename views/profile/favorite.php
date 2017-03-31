<?php

/* @var $this yii\web\View */
/* @var $favorites[] app\models\Favorite */

use yii\helpers\Html;
use yii\helpers\Url;
use app\assets\WeAsset;

WeAsset::register($this);

$favCss = <<<EOF
.weui-panel {
    margin-top: 0px;
}

.weui-media-box {
    padding-top: 5px;
    padding-bottom: 5px;
    padding-left: 10px;
    padding-right: 5px;
}

.weui-panel__bd img {
    width: 50px;
    height: 50px;
}
EOF;
$this->registerCss($favCss);

$deleteJs = <<<EOF
$('.delete-favorite').click(function() {
    $.ajax({
        url: '/favorite/delete?id=' + $(this).attr('favorite-id'),
        method: 'DELETE',
        dataType: 'json',
        success: function(res) {
            console.log(res);
        }
    });
    location.reload();
});
EOF;
$this->registerJs($deleteJs);

?>

<div class="container store-viewport" id="user-profile">
    <?= $this->render('_thumb', ['user' => $user]) ?>

    <?php foreach ($favorites as $favorite): ?>
    <div class="weui-panel weui-panel_access">
        <div class="weui-panel__bd">
            <div class="weui-media-box weui-media-box_appmsg">
                <div class="weui-media-box__hd">
                    <?= Html::img($favorite->item->images[0]->photo_path) ?>
                </div>
                <div class="weui-media-box__bd">
                    <div class="weui-cell">
                        <a href="<?= $favorite->uri ?>" class="weui-cell__bd">
                            <?= Html::tag('div', $favorite->item->name,
                                          ['class' => 'weui-media-box__title']) ?>
                            <?php if ($favorite->groupOrder): ?>
                                <?= Html::tag('div', '团购订单：' . $favorite->groupOrder->id,
                                              ['class' => 'weui-media-box__desc']) ?>
                            <?php endif; ?>
                        </a>
                        <button favorite-id="<?=$favorite->id ?>"
                                class="weui-cell__ft btn btn-danger delete-favorite">
                            <span class="glyphicon glyphicon-trash"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>  
    <?php endforeach; ?>

    <div class="weui-cell">
        <p class="weui-cell__bd"></p>
        <a class="btn btn-primary weui-cell__ft"
           id="return-home" href="/profile/index">
            <span class="glyphicon glyphicon-arrow-left"> 返回</span>
        </a>
    </div>
    
</div>
