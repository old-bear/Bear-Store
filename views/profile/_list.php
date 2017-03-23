<?php

/* @var $this yii\web\View */
/* @var $order[] app\models\GroupOrder */

use yii\helpers\Html;
use yii\helpers\Url;

$formatter = \Yii::$app->formatter;

?>

<?php foreach ($orders as $order): ?>
<div class="row order-list-item-container">
    <div class="order-list-item">
        <div class="item-image text-center">
            <div class="square">
                <div class="innerthumbnail">
                    <?= Html::img($order->item->images[0]->photo_path) ?>  
                </div>
            </div>
        </div>

        <div class="row item-title">
            <?php if (!$delete): ?>
            <a href = '<?= Url::to(['group-order/view', 'id' => $order->id], true)?>' >
                <?= Html::encode($order->item->name) ?>
            </a>
            <?php else: ?>
            <div class="col-xs-10 remove-padding">
                <a href = '<?= Url::to(['group-order/view', 'id' => $order->id], true)?>' >
                    <?= Html::encode($order->item->name) ?>
                </a>
            </div>
            <div class="col-xs-2">
                <button class="btn btn-primary delete-favorite-order" value="<?= $order->id ?>">
                    <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
                </button>
            </div>
            <?php endif; ?>
        </div>
        <div class="row item-detail">
            <div class="row">
                <div class="col-xs-2 item-label">订单号</div>
                <div class="col-xs-10 customer-order-list">
                    <?php foreach ($order->fetchCustomerOrderID($user->id) as $pay_id): ?>
                    <div class="row remove-margin"><?= Html::encode($pay_id) ?></div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-2 item-label">预计</div>
                <div class="col-xs-10 order-delivery-date"><?= $formatter->asDate($order->arrival_date) ?></div>
            </div>
            <div class="row">
                <div class="col-xs-2 item-label">送至</div>
                <div class="col-xs-10 order-address"><?= Html::encode($order->deliveryAddressString()) ?></div>
            </div>
        </div>
    </div>
    <div class="row separator"></div>
</div>
<?php endforeach; ?>
