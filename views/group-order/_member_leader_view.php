<?php

/* @var $this yii\web\View */
/* @var $order app\models\GroupOrder */
/* @var $user app\models\Customer */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use app\models\Customer;

use yii\widgets\ListView;

$formatter = \Yii::$app->formatter;

$groupCss = <<<EOF
#group-detail img {
    width: 50px;
}

#group-detail hr {
    width: 200%;
    margin-top: 6px;
    margin-bottom: 7px;
}

#leader {
    margin-bottom: 10px;
}

#tuanzhang {
    width: 50px;
    opacity: 0.7;
}

.member-row {
    margin-bottom: 8px;
}

.member-label {
    margin-left: -15px;
    padding-top: 15px;
}

.order-status {
    color: green;
    padding-top: 15px;
}
EOF;
$this->registerCss($groupCss);

$members = [$order->leader_id => true];
foreach ($order->getCustomerOrders()->all() as $co) {
    if (!isset($members[$co->customer_id])) {
        // Only need to check one customer order's status since we set all
        // customer orders of a user to delivered when leader scans the QRCode
        $members[$co->customer_id] = ['complete' => $co->status == 'delivered',
                                      'user' => Customer::findOne($co->customer_id)];
    }
}
unset($members[$order->leader_id]);

?>

<div id="group-detail">
    <div id="leader" class="row">
        <div class="col-xs-2 item-label">成员</div>
        <div class="col-xs-3">
            <?= Html::img($order->leader->head_img_path, ['class'=>'img-rounded']) ?>
        </div>
        <div class="col-xs-4">
            <div class="row"><?= Html::encode($order->leader->nick_name) ?></div>
            <hr class="row"></hr>
            <div class="row"><?= Html::encode($order->leader->phone) ?></div>
        </div>
        <div class="col-xs-3">
            <img id="tuanzhang" src="/images/tuanzhang.png" />
        </div>
    </div>

    <?php foreach ($members as $member): ?>
        <div class="row member-row">
            <div class="col-xs-2 item-label"></div>
            <div class="col-xs-3">
                <?= Html::img($member['user']->head_img_path, ['class'=>'img-circle']) ?>
            </div>
            <div class="col-xs-5 member-label">
                <?= Html::encode($member['user']->nick_name) ?>
            </div>
            <?php if ($member['complete']): ?>
                <div class="col-xs-2 order-status">
                    <span class="glyphicon glyphicon-ok"></span>
                </div>
            <?php endif ?>
        </div>
    <?php endforeach; ?>
</div>
