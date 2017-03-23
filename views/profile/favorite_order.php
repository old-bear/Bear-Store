<?php

/* @var $this yii\web\View */
/* @var $order[] app\models\GroupOrder */

use yii\helpers\Html;
use yii\helpers\Url;

$formatter = \Yii::$app->formatter;
$favoriteItemURL = Url::to(['profile/favorite-item'], true);
?>

<div class="container store-viewport">
    <div class="order-nav-container">
        <ul class="nav nav-tabs order-nav">
            <li role="presentation">
                <a href="<?= $favoriteItemURL ?>">商品收藏</a></li>
            <li role="presentation" class="active">
                <a href="#">团购收藏</a></li>
        </ul>
    </div>
    
    <?= $this->render('_list', [
        'user' => $user,
        'orders' => $orders,
        'delete' => true,
    ])?>
  
</div>