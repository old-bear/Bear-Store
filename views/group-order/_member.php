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
    width: 40px;
}

#group-detail hr {
    width: 200%;
    margin-top: 6px;
    margin-bottom: 7px;
}

#leader {
    margin-bottom: 10px;
}

#leader img {
   width: 50px;
}

#tuanzhang {
    width: 50px;
    opacity: 0.7;
}

.member-label {
    font-size: 12px;
    text-align: center;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

#ellipsis {
    font-size: 30px;
    padding-top: 16px;
}
EOF;
$this->registerCss($groupCss);

$members = [$order->leader_id => true];
if ($order->getCustomerOrder($user->id)) {
    // Include the user himself first
    if (!isset($members[$user->id])) {
        $members[$user->id] = Customer::findOne($user->id);
    }
}
$maxMember = 3;
foreach ($order->getCustomerOrders()
               ->orderBy('last_modified_time DESC')->all() as $co) {
    if (!isset($members[$co->customer_id])) {
        $members[$co->customer_id] = Customer::findOne($co->customer_id);
        if (count($members) == $maxMember + 1) {    // Plus the leader
            break;
        }
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

    <?php if ($members): ?>
    <div class="row">
        <div class="col-xs-2 item-label"></div>
        <div class="col-xs-10">
            <div class="row">
            <?php foreach ($members as $member): ?>
                <div class="col-xs-3">
                    <div class="text-center">
                        <?= Html::img($member->head_img_path, ['class'=>'img-circle']) ?>
                    </div>
                    <div class="member-label"><?= Html::encode($member->nick_name) ?></div> 
                </div>
            <?php endforeach; ?>
            <div id="ellipsis" class="col-xs-3">...</div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
