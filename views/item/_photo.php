<?php

/* @var $this yii\web\View */
/* @var $model app\models\Item */

use yii\helpers\Html;
use app\assets\SwiperAsset;

SwiperAsset::register($this);

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
