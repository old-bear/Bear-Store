<?php

/* @var $this yii\web\View */
/* @var $user app\models\Customer */

use yii\helpers\Html;
use yii\helpers\Url;

$formatter = \Yii::$app->formatter;

?>

<div id="user-info">
    <div class="panel panel-default">
        <div class="panel-heading">
            <?= Html::img($user->head_img_path) ?> <br>
            <div class="separator"></div>
            <b><?= Html::encode($user->nick_name) ?></b><br>
            <?php if ($user->email): ?>
                <div class="row">
                    <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                        <span class="glyphicon glyphicon-phone" aria-hidden="true"> <?= Html::encode($user->phone) ?> </span>
                    </div>
                    <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
                        <span class="glyphicon glyphicon-envelope" aria-hidden="true"> <?= Html::encode($user->email) ?> </span>
                    </div>
                </div>
            <?php else: ?>
                <span class="glyphicon glyphicon-phone" aria-hidden="true"> <?= Html::encode($user->phone) ?> </span>
            <?php endif; ?>
        </div>
        <div class="panel-body">
            <div class="row text-center">
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                    余额: <?= $formatter->asCurrency($user->balance) ?>
                </div>
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                    积分: <?= Html::encode($user->bonus_points) ?>
                </div>
            </div>
        </div>
    </div>       
</div>

<div class="row separator" id="user-separator"></div>