<?php

/* @var $this yii\web\View */
/* @var $user app\models\Customer */

use yii\helpers\Html;
use yii\helpers\Url;

use yii\bootstrap\Modal;

$formatter = \Yii::$app->formatter;

$addressManagementURL = Url::to(['profile/address-management'], true);
$orderManagementURL = Url::to(['profile/order-management'], true);
$favoriteItemURL = Url::to(['profile/favorite-item'], true);

$qrcode_img_url = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='
    . $user->qrTicket;

?>

<div class="container store-viewport" id="user-profile">
    <?= $this->render('_detail', ['user' => $user]) ?>
  
    <?php
    Modal::begin([
        'header' => '<h3>二维码名片</h3>',
        'toggleButton' => ['label' => '二维码名片',
                           'id' => 'qrcode-toggle-btn'],
    ]);
  
    echo Html::img($qrcode_img_url);
  
    Modal::end();
    ?>
  
    <div class="row separator"></div>

    <ul class="list-group">
        <li class="row list-group-item">
            <a href="<?= $orderManagementURL ?>">
                <div class="col-xs-11 col-sm-11 col-md-11 col-lg-11">
                    <span class="glyphicon glyphicon-shopping-cart" aria-hidden="true">
                        订单管理
                    </span>
                </div>
                <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 text-right">
                    <span class="glyphicon glyphicon-chevron-right text-grey" aria-hidden="true"></span>
                </div>
            </a>
        </li>
        <li class="row list-group-item">
            <a href="<?= $favoriteItemURL ?>">
                <div class="col-xs-11 col-sm-11 col-md-11 col-lg-11">
                    <span class="glyphicon glyphicon-heart" aria-hidden="true">
                        收藏管理
                    </span>
                </div>
                <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 text-right">
                    <span class="glyphicon glyphicon-chevron-right text-grey" aria-hidden="true"></span>
                </div>
            </a>
        </li>
        <li class="row list-group-item">
            <a href="<?= $addressManagementURL ?>">
                <div class="col-xs-11 col-sm-11 col-md-11 col-lg-11">
                    <span class="glyphicon glyphicon-map-marker" aria-hidden="true">
                        地址管理
                    </span>
                </div>
                <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 text-right">
                    <span class="glyphicon glyphicon-chevron-right text-grey" aria-hidden="true"></span>
                </div>
            </a>
        </li>
    </ul>

    <div class="row separator"></div>

</div>