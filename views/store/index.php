<?php

/* @var $this yii\web\View */
/* @var $model[] app\models\Item */

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;

$formatter = \Yii::$app->formatter;

$string = file_get_contents("raw_data.json");
$raw_data = json_decode($string, true);

$raw_data = $raw_data['portalData']['list'];

$fake_data = array();
foreach ($raw_data as $item) {
    if (isset($item['data'])) {
        $name = $item['data']['shortGoodsName'];
        $price = $item['data']['price'];
        $image = $item['data']['imgUrl'];

        $data = [
            'name' => $name,
            'price' => $price,
            'image' => $image,
        ];
        array_push($fake_data, $data);
    }
    if (count($fake_data) == 20) break;
}

?>

<div class="container store-viewport store-index">
  
    <!-- search bar -->
    <div class="weui-search-bar" id="search_bar">
        <form class="weui-search-bar__form">
            <div class="weui-search-bar__box">
                <i class="weui-icon-search"></i>
                <input type="search" class="weui-search-bar__input" id="search_input" name="name" placeholder="搜索" />
                <a href="javascript:" class="weui-icon-clear" id="search_clear"></a>
            </div>
            <label for="search_input" class="weui-search-bar__label" id="search_text">
                <i class="weui-icon-search"></i>
                <span>搜索</span>
            </label>
        </form>
        <a href="javascript:" class="weui-search-bar__cancel-btn" id="search_cancel">取消</a>
    </div>
  
    <!-- item list -->
    <div class="store-item-list">
        <?php foreach ($models as $model): ?>
        <div class="row store-item-container">
            <div class="store-item">
                <div class="item-image text-center">
                    <div class="square">
                        <div class="innerthumbnail">
                            <?= Html::img($model->images[0]->photo_path) ?>  
                        </div>
                    </div>
                </div>

                <div class="row item-title">
                    <div class="row title">
                        <?= Html::encode($model->name) ?>
                    </div>
                    <div class="row">
                        <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8 price">
                              <?= $formatter->asCurrency($model->price) ?>
                        </div>
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 text-right view-item-btn">
                            <a href="<?= Url::to(['item/view', 'id' => $model->id], true)?>">
                                <button class="btn btn-danger">去参团 <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span></button>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row separator"></div>
        <?php endforeach; ?>
      
        <?php foreach ($fake_data as $data): ?>
        <div class="row store-item-container">
            <div class="store-item">
                <div class="item-image text-center">
                    <div class="square">
                        <div class="innerthumbnail">
                            <?php echo '<img class="store-item-image" src="' . $data['image'] . '">' ?>  
                        </div>
                    </div>
                </div>

                <div class="row item-title">
                    <div class="row title">
                        <?= Html::encode($data['name']) ?>
                    </div>
                    <div class="row">
                        <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8 price">
                              <?= $formatter->asCurrency($data['price']) ?>
                        </div>
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 text-right view-item-btn">
                            <a href="<?= Url::to(['item/view', 'id' => 1], true)?>">
                                <button class="btn btn-danger">去参团 <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span></button>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row separator"></div>
        <?php endforeach; ?>
      
    </div> 
</div>
