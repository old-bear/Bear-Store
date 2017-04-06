<?php

/* @var $this yii\web\View */

$this->title = '友团有限公司';
?>
<div>
    <div class="jumbotron">
        <h1>芝麻开门</h1>
        <?php if (Yii::$app->user->isGuest): ?>
            <a href="/admin/login"><img src='/images/door.jpg' /></a>
        <?php else: ?>
            <img src='/images/treasure.jpg' />
        <?php endif; ?>
    </div>
</div>
