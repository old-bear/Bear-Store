<?php

/* @var $this yii\web\View */
/* @var $user app\models\Customer */
/* @var $address app\models\CustomerAddress*/

use yii\helpers\Html;
use yii\helpers\Url;

?>

<div class="container store-viewport" id="user-profile">  
    <?= $this->render('_detail', ['user' => $user]) ?>
    
    <ul class="list-group address-list">
        <?php foreach($addresses as $address): ?>
        <li class="list-group-item">
            <div class="row">
                <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
                    <?= Html::encode($address->addressString()) ?>
                </div>
                <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 text-right">
                    <?= Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>', 
                                Url::to(['customer-address/update', 'id' => $address->id], true)) ?>
                </div>
                <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 text-right">
                    <span class="glyphicon glyphicon-trash delete-address" value="<?= $address->id?>" aria-hidden="true"></span>
                </div>
            </div>
            <div class="row text-grey contact-info">
                <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                    <span class="glyphicon glyphicon-user" aria-hidden="true"> <?= Html::encode($address->contact_name) ?> </span>
                </div>
                <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
                    <span class="glyphicon glyphicon-phone" aria-hidden="true"> <?= Html::encode($address->contact_phone) ?> </span>
                </div>
            </div>
        </li>
        <?php endforeach; ?>
    </ul>
    
    <div class="row separator"></div>
  
    <div class="btn-group btn-group-justified">
        <div class="btn-group" role="group">
            <a href="<?= Url::to(['customer-address/create', 'redirectUrl' => Url::current()], true)?>">
                <button class="btn btn-danger" id="add-new-address">
                    <span class="glyphicon glyphicon-plus" aria-hidden="true"> 添加新地址 </span>
                </button>
            </a>
        </div>
    </div>
  
</div>
