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
class SwiperAsset extends AssetBundle
{
    // public $sourcePath = '@npm/swiper/dist';
    public $css = [
        '//cdn.bootcss.com/Swiper/3.4.0/css/swiper.min.css',
    ];
    public $js = [
        '//cdn.bootcss.com/Swiper/3.4.0/js/swiper.jquery.min.js',
    ];
    public $depends = [
        // Depends on JQuery
        'yii\web\YiiAsset',
    ];
}
