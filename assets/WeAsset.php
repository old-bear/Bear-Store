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
class WeAsset extends AssetBundle
{
    public $sourcePath = '@bower/weui/dist/style';
    public $css = [
        'weui.css',
    ];
    public $js = [
        'http://res.wx.qq.com/open/js/jweixin-1.0.0.js',
    ];
}
