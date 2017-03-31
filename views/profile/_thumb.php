<?php

/* @var $this yii\web\View */
/* @var $user app\models\Customer */

use yii\helpers\Html;
use yii\helpers\Url;

$formatter = \Yii::$app->formatter;

$infoCss = <<<EOF
#user-info .panel {
    border-radius: 0;
    border: 0;
    box-shadow: 0 0 0;
    margin: 0;
}

#user-info .panel-heading {
    border-radius: 0;
    background-color: #ff7500;
    color: white;
    text-align: center;
    border: 0;
}

#user-info .panel-heading img {
    width: 96px;
    height: 96px;
    border-radius: 48px;
}

#user-info .panel-heading .separator {
    height: 5px;
}

#user-separator {
    margin-top: 0px;
    margin-bottom: 0px;
}
EOF;
$this->registerCss($infoCss);

?>

<div id="user-info">
    <div class="panel panel-default">
        <div class="panel-heading">
            <?= Html::img($user->head_img_path) ?> <br>
            <div class="separator"></div>
            <b><?= Html::encode($user->nick_name) ?></b><br>
        </div>
        <div class="panel-body">
            <div class="row text-center">
                <div class="col-xs-6">
                    余额: <?= $formatter->asCurrency($user->balance) ?>
                </div>
                <div class="col-xs-6">
                    积分: <?= Html::encode($user->bonus_points) ?>
                </div>
            </div>
        </div>
    </div>       
</div>

<div class="row separator" id="user-separator"></div>
