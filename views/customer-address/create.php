<?php

/* @var $this yii\web\View */
/* @var $model app\models\CustomerAddress */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\models\Area;

$this->title = '添加收货地址';

$selectJs = <<<EOF
$('#address-selection').cascadingDropdown({
    selectBoxes: [
        {
            selector: '#province',
            source: function(request, response) {
                $.getJSON('/area/list-province', request, function(data) {
                    data.unshift({label: '', value: 0, selected: true});
                    response(data);
                });
            }
        },
        {
            selector: '#city',
            requires: ['#province'],
            source: function(request, response) {
                $.getJSON('/area/list-city', request, function(data) {
                    if (data.length > 0) {
                        data.unshift({label: '', value: 0, selected: true});
                    }
                    response(data);
                });
            }
        },
        {
            selector: '#district',
            requires: ['#city'],
            source: function(request, response) {
                $.getJSON('/area/list-district', request, function(data) {
                    response(data);
                });
            }
        },
    ],
    onReady: function() {
        if ($('#district-input').length > 0) {
            $('#province').val($('input[name=province-input]').val()).trigger('change');
            setTimeout(function() {
                $('#city').val($('input[name=city-input]').val()).trigger('change');
            }, 1000);
            setTimeout(function() {
                $('#district').val($('input[name=district-input]').val()).trigger('change');
            }, 1000);
        }
    }
});
EOF;
$this->registerJs($selectJs);

$addressCss = <<<EOF
#address-form, #user-form {
    margin-top: 10px;
}

#address-form .col-xs-3, #user-form .col-xs-3 {
    padding-left: 10px;
    padding-right: 0px;
}

#address-form .control-label, #user-form .control-label {
    padding-top: 5px;
    margin-bottom: 0px;
    font-weight: normal;
    color: grey;
}

#address-form .row, #user-form .row {
    margin-bottom: 10px;
}

#address-selection .row:last-child {
    margin-top: -5px;
    margin-bottom: 5px;
}
EOF;
$this->registerCss($addressCss);

?>

<div class="container store-viewport">
    <?php $form = ActiveForm::begin(['id' => "address-form"]); ?>
    <div id="address-selection">
    <div class="row">
        <div class="form-group col-xs-3 text-right required">
            <label class="control-label">省/直辖市</label>
        </div>
        <div class="col-xs-9">
            <?= Html::dropDownList("provinceID", null, [],
                                   ["id" => "province", "class" => "form-control"]) ?>
        </div>
    </div>
    
    <div class="row">
        <div class="form-group col-xs-3 text-right required">
            <label class="control-label">城市</label>
        </div>
        <div class="col-xs-9">
            <?= Html::dropDownList("cityID", null, [],
                                   ["id" => "city", "class" => "form-control"]) ?>
        </div>
    </div>

    <div class="row">
        <div class="form-group col-xs-3 text-right required">
            <label class="control-label">地区</label>
        </div>
        <div class="col-xs-9">
            <?= $form->field($model, 'district_id', [])
                     ->dropDownList([], ["id" => "district",
                                         "class" => "form-control"])->label(false) ?>
        </div>
    </div>

    <div class="row">
        <div class="form-group col-xs-3 text-right required">
            <label class="control-label">详细地址</label>
        </div>
        <div class="col-xs-9">
            <?= $form->field($model, 'address')
             ->textInput(['maxlength' => true])->label(false) ?>
        </div>
    </div>
    </div>

    <div class="row separator"></div>
    
    <div class="row">
        <div class="form-group col-xs-3 text-right required">
            <label class="control-label">联系人</label>
        </div>
        <div class="col-xs-9">
            <?= $form->field($model, 'contact_name')
             ->textInput(['maxlength' => true])->label(false) ?>
        </div>
    </div>

    <div class="row">
        <div class="form-group col-xs-3 text-right required">
            <label class="control-label">联系电话</label>
        </div>
        <div class="col-xs-9">
            <?= $form->field($model, 'contact_phone')->input('number')->label(false) ?>
        </div>
    </div>

    <div class="row">
        <div class="form-group col-xs-3 text-right">
            <label class="control-label">备注信息</label>
        </div>
        <div class="col-xs-9">
            <?= $form->field($model, 'note')
             ->textInput(['maxlength' => true])->label(false) ?>
        </div>
    </div>
        
    <div class="btn-group btn-group-justified">
        <div class="btn-group" role="group">
            <button type="submit" class="btn btn-danger">
                <?php if ($province && $city && $district): ?> 
                确认更新
                <?php else: ?>
                确认添加
                <?php endif; ?>
            </button>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
  
    <?php if ($province && $city && $district): ?>
    <div id="district-input" class="hidden">
        <?= Html::input('text', 'province-input', $province) ?>
        <?= Html::input('text', 'city-input', $city) ?>
        <?= Html::input('text', 'district-input', $district) ?>
    </div>
    <?php endif; ?>

</div>
