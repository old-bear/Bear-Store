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
class CascadingDropdownAsset extends AssetBundle
{
    public $sourcePath = '@bower/jquery-cascading-dropdown/';
    public $js = [
        'jquery.cascadingdropdown.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
    ];
}
