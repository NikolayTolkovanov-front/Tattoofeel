<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace frontend\assets;

use common\assets\Html5shiv;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;

/**
 * Frontend application asset
 */
class FrontendAsset extends AssetBundle
{
    /**
     * @var string
     */
//    public $sourcePath = '@frontend/web';
    public $basePath = '@frontend/web';
    //public $baseUrl = '@frontendUrl';

    /**
     * @var array
     */
    public $css = [
        // 'fonts/stylesheet.css',
        // 'css/font-awesome.min.css',
//        YII_ENV_PROD ? 'css/style.css' : 'css/style.less',
        // 'css/style.css',
        // 'css/mobile.css',
        // 'css/app.css',
        // 'css/query.modal.min.css',
        // 'css/swiper.min.css',
        // 'css/custom.css',
    ];

    /**
     * @var array
     */
    public $js = [
        // end
        'js/inputmask.ex.js',
        'js/app.js',
        'js/jquery.modal.min.js',
        'js/swiper.min.js',
        'js/custom.js',
    ];

    /**
     * @var array
     */
    public $depends = [
        //YiiAsset::class,
        //BootstrapAsset::class,
        Html5shiv::class,
        JqueryAsset::class,
        FrontendResetAsset::class,
        FrontendSlickAsset::class,
        FrontendRangeAsset::class,
        FrontendJMaskAsset::class,
    ];
}
