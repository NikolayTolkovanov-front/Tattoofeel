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
class FrontendRangeAsset extends AssetBundle
{

    /**
     * @var string
     */
//    public $sourcePath = '@frontend/web';
    public $basePath = '@frontend/web';

    /**
     * @var array
     */
    public $css = [
        'css/jquery.range.css',
    ];

    /**
     * @var array
     */
    public $js = [
        'js/jquery.range-min.js',
    ];
}
