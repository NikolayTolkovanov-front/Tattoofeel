<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Frontend application asset
 */
class FrontendSlickAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@bower';

    /**
     * @var array
     */
    public $css = [
        'slick-carousel/slick/slick.css',
    ];

    /**
     * @var array
     */
    public $js = [
        'slick-carousel/slick/slick.min.js',
    ];
}
