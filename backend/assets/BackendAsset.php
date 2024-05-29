<?php
/**
 * Created by PhpStorm.
 * User: zein
 * Date: 7/3/14
 * Time: 3:14 PM
 */

namespace backend\assets;

use common\assets\AdminLte;
use common\assets\Html5shiv;
use frontend\assets\FrontendJMaskAsset;
use yii\web\AssetBundle;
use yii\web\YiiAsset;

class BackendAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $basePath = '@webroot';
    /**
     * @var string
     */
    public $baseUrl = '@web';

    /**
     * @var array
     */
    public $css = [
        'css/style.css',
        'css/order-products.css',
        'css/product-related.css',
    ];
    /**
     * @var array
     */
    public $js = [
        'js/app.js',
        'js/upload-kit.js',
        'js/order-products.js',
        'js/product-related.js',
    ];

    /**
     * @var array
     */
    public $depends = [
        YiiAsset::class,
        AdminLte::class,
        Html5shiv::class,
        BackendJMaskAsset::class
    ];
}
