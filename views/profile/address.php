<?php

/* @var $this yii\web\View */
/* @var $user app\models\Customer */
/* @var $address app\models\CustomerAddress*/

use yii\helpers\Html;
use yii\helpers\Url;

$addressCss = <<<EOF
.address-list .glyphicon-trash {
    color: #f20c00;
}

.address-list .glyphicon-pencil {
    color: #5cf;
}

.list-group-item .row {
    margin-right: 0px;
}
EOF;
$this->registerCss($addressCss);

?>

<div class="container store-viewport" id="user-profile">  
    <?= $this->render('_thumb', ['user' => $user]) ?>
    
    <ul class="list-group address-list">
        <?php foreach($addresses as $address): ?>
        <li class="list-group-item">
            <div class="row">
                <div class="col-xs-10">
                    <?= Html::encode($address->addressString()) ?>
                </div>
                <div class="col-xs-1 text-right">
                    <?= Html::a('<span class="glyphicon glyphicon-pencil"></span>', 
                                Url::to(['customer-address/update', 'id' => $address->id], true)) ?>
                </div>
                <div class="col-xs-1 text-right">
                    <span class="glyphicon glyphicon-trash delete-address" value="<?= $address->id?>"></span>
                </div>
            </div>
            <div class="row text-grey contact-info">
                <div class="col-xs-5">
                    <span class="glyphicon glyphicon-user">
                        <?= Html::encode($address->contact_name) ?>
                    </span>
                </div>
                <div class="col-xs-7">
                    <span class="glyphicon glyphicon-phone">
                        <?= Html::encode($address->contact_phone) ?>
                    </span>
                </div>
            </div>
        </li>
        <?php endforeach; ?>
    </ul>
    
    <div class="row separator"></div>
  
    <div class="row">
        <a class="col-xs-4 col-xs-offset-2 btn btn-primary" href="/profile/index">
            <span class="glyphicon glyphicon-arrow-left"> 返回</span>
        </a>
        <a class="col-xs-4 col-xs-offset-1 btn btn-danger"
           href="<?= Url::to(['customer-address/create',
                              'redirectUrl' => Url::current()], true)?>">
            <span class="glyphicon glyphicon-plus"> 添加新地址</span>
        </a>
    </div>
  
</div>
