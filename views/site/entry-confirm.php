<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'Entry';
$this->params['breadcrumbs'][] = $this->title;
?>

<p>You have entered the following information:</p>
<ul>
  <li><label>Name</label>: <?= Html::encode($model->name); ?></li>
  <li><label>Email</label>: <?= Html::encode($model->email); ?></li>
</ul>