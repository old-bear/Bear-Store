<?php

/* @var $this yii\web\View */
/* @var $order[] app\models\GroupOrder */

use yii\helpers\Html;
use yii\helpers\Url;

$allURL = Url::current(['status' => null]);
$creatingURL = Url::current(['status' => 'creating']);
$createdURL = Url::current(['status' => 'created']);
$deliveringURL = Url::current(['status' => 'delivering']);
$deliveredURL = Url::current(['status' => 'delivered']);
$completedURL = Url::current(['status' => 'completed']);
$cancelledURL = Url::current(['status' => 'cancelled']);

?>

<div class="container store-viewport">
    <div class="order-nav-container">
        <ul class="nav nav-tabs order-nav">
            <li role="presentation" <?php if (!$status): echo 'class="active"'; endif; ?>>
                <a href="<?= $allURL ?>">全部</a></li>
            <li role="presentation" <?php if ($status == 'creating'): echo 'class="active"'; endif; ?>>
                <a href="<?= $creatingURL ?>">待成团</a></li>
            <li role="presentation" <?php if ($status == 'created'): echo 'class="active"'; endif; ?>>
                <a href="<?= $createdURL ?>">待发货</a></li>
            <li role="presentation" <?php if ($status == 'delivering'): echo 'class="active"'; endif; ?>>
                <a href="<?= $deliveringURL ?>">待收货</a></li>
            <li role="presentation" <?php if ($status == 'delivered'): echo 'class="active"'; endif; ?>>
                <a href="<?= $deliveredURL ?>">待分发</a></li>
            <li role="presentation" <?php if ($status == 'completed'): echo 'class="active"'; endif; ?>>
                <a href="<?= $completedURL ?>">已完成</a></li>
            <li role="presentation" <?php if ($status == 'cancelled'): echo 'class="active"'; endif; ?>>
                <a href="<?= $cancelledURL ?>">已取消</a></li>
        </ul>
    </div>
  
    <?= $this->render('_list', [
        'user' => $user,
        'orders' => $orders,
        'delete' => false,
    ])?>
  
</div>