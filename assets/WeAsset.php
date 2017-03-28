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
        '//res.wx.qq.com/open/libs/weui/1.1.1/weui.min.css',
    ];
    public $js = [
        'http://res.wx.qq.com/open/js/jweixin-1.1.0.js',
    ];
}
