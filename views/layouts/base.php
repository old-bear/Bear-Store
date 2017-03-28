<?php

/* @var $this yii\web\View */
/* @var $content string */

use app\assets\AppAsset;
use app\assets\TouchSpinAsset;
use app\assets\DatepickerAsset;
use app\assets\CascadingDropdownAsset;
use yii\helpers\Html;

TouchSpinAsset::register($this);
DatepickerAsset::register($this);
CascadingDropdownAsset::register($this);
AppAsset::register($this);

$title = $this->title ? $this->title : '从前有个团';
?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1 maximum-scale=1, user-scalable=no">
    <?= Html::csrfMetaTags() ?>
    <?= Html::tag('title', $title) ?>
    <?php $this->head() ?>
</head>
<body>
    <?php $this->beginBody() ?>
    <div style='margin:0 auto; width:0px; height:0px; overflow:hidden;'>
        <img src="/images/icon.png">
    </div>
    <?= $content ?>
    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
