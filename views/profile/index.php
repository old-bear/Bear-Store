<?php

/* @var $this yii\web\View */
/* @var $user app\models\Customer */

use yii\helpers\Html;
use yii\helpers\Url;

use yii\bootstrap\Modal;

$formatter = \Yii::$app->formatter;

$addressManagementURL = Url::to(['profile/address-management'], true);
$orderManagementURL = Url::to(['profile/order-management'], true);
$favoriteItemURL = Url::to(['profile/favorite-management'], true);

$qrcodeImgLink = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . $user->qrTicket;

?>

<div class="container store-viewport" id="user-profile">
    <?= $this->render('_thumb', ['user' => $user]) ?>
  
    <?php
    Modal::begin([
        'header' => '<h3>我的二维码</h3>',
        'toggleButton' => ['label' => ' 我的二维码',
                           'class' => 'glyphicon glyphicon-qrcode',
                           'id' => 'qrcode-toggle-btn'],
    ]);
  
    echo Html::img($qrcodeImgLink);
  
    Modal::end();
    ?>
  
    <div class="row separator"></div>

    <ul class="list-group">
        <li class="row list-group-item">
            <a href="<?= $orderManagementURL ?>">
                <div class="col-xs-10">
                    <span class="glyphicon glyphicon-shopping-cart">
                        订单管理
                    </span>
                </div>
                <div class="col-xs-2 text-right">
                    <span class="glyphicon glyphicon-chevron-right text-grey"></span>
                </div>
            </a>
        </li>
        <li class="row list-group-item">
            <a href="<?= $favoriteItemURL ?>">
                <div class="col-xs-10">
                    <span class="glyphicon glyphicon-heart">
                        收藏管理
                    </span>
                </div>
                <div class="col-xs-2 text-right">
                    <span class="glyphicon glyphicon-chevron-right text-grey"></span>
                </div>
            </a>
        </li>
        <li class="row list-group-item">
            <a href="<?= $addressManagementURL ?>">
                <div class="col-xs-10">
                    <span class="glyphicon glyphicon-map-marker">
                        地址管理
                    </span>
                </div>
                <div class="col-xs-2 text-right">
                    <span class="glyphicon glyphicon-chevron-right text-grey"></span>
                </div>
            </a>
        </li>
    </ul>

    <div class="row separator"></div>

</div>
