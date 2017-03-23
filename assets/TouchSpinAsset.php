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
class TouchSpinAsset extends AssetBundle
{
    public $sourcePath = '@npm/bootstrap-touchspin/dist';
    public $css = [
        'jquery.bootstrap-touchspin.min.css',
    ];
    public $js = [
        'jquery.bootstrap-touchspin.min.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
    ];
}
