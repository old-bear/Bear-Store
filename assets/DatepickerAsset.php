<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class DatepickerAsset extends AssetBundle
{
    public $sourcePath = '@bower/bootstrap-datepicker/dist';
    public $css = [
        '//cdn.bootcss.com/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker3.min.css',
    ];
    public $js = [
        '//cdn.bootcss.com/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.min.js',
        '//cdn.bootcss.com/bootstrap-datepicker/1.6.4/locales/bootstrap-datepicker.zh-CN.min.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
    ];
}
