<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Entry';
$this->params['breadcrumbs'][] = $this->title;
?>

<?php $form = ActiveForm::begin(); ?>
  <?= $form->field($model, 'name') ?>
  <?= $form->field($model, 'email') ?>

  <div class="form-group">
    <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
  </div>
<?php ActiveForm::end(); ?>
