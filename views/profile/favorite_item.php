<?php

/* @var $this yii\web\View */
/* @var $order[] app\models\GroupOrder */

use yii\helpers\Html;
use yii\helpers\Url;

$formatter = \Yii::$app->formatter;
$favoriteOrderURL = Url::to(['profile/favorite-order'], true);
?>

<div class="container store-viewport">
    <div class="order-nav-container">
        <ul class="nav nav-tabs order-nav">
            <li role="presentation" class="active">
                <a href="#">商品收藏</a></li>
            <li role="presentation">
                <a href="<?= $favoriteOrderURL ?>">团购收藏</a></li>
        </ul>
    </div>
  
    <?php foreach ($items as $model): ?>
    <div class="row favorite-item-container">
        <div class="favorite-item">
            <div class="item-image text-center">
                <div class="square">
                    <div class="innerthumbnail">
                        <?= Html::img($model->images[0]->photo_path) ?>  
                    </div>
                </div>
            </div>

            <div class="row item-title">
                <div class="row title">
                    <?= Html::encode($model->name) ?>
                </div>
                <div class="row">
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 price">
                          <?= $formatter->asCurrency($model->price) ?>
                    </div>
                    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 text-right view-item-btn">
                        <a href="<?= Url::to(['item/view', 'id' => $model->id], true)?>">
                            <button class="btn btn-danger">去参团 <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span></button>
                        </a>
                    </div>
                    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 text-right">
                        <button class="btn btn-primary delete-favorite-item" value="<?= $model->id ?>">
                            <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="row separator"></div>
    </div>
    <?php endforeach; ?>
  
</div>