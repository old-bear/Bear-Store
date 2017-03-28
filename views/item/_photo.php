<?php

/* @var $this yii\web\View */
/* @var $model app\models\Item */

use yii\helpers\Html;
use app\assets\SwiperAsset;

SwiperAsset::register($this);

$swiper = <<<EOF
var mySwiper = new Swiper('.swiper-container', {
    autoplay: 3000,
    speed: 1000,
    loop: true,
    effect: "slide",
    touchRatio: 1.5,
    pagination: '.swiper-pagination',
    paginationClickable: true,
    paginationHide: false,
});
EOF;
$this->registerJs($swiper);

$photoCss = <<<EOF
#item-photo {
    margin-left: -15px;
    margin-right: -15px;
    margin-bottom: -5px;
    background-color: #eeeeee;
}

.swiper-pagination-bullet-active {
    background-color: #ff2121;
}
EOF;
$this->registerCss($photoCss);

?>

<div id="item-photo" class="row swiper-container">
    <div class="swiper-wrapper">
    <?php
    foreach ($model->images as $img) {
        echo '<div class="swiper-slide">';
        echo '<img class="img-responsive" src="' . $img->photo_path . '"/>';
        echo '</div>';
    }
    ?> 
    </div>
    <div class="swiper-pagination"></div>
</div>
