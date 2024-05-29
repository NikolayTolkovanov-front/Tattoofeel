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
class MaintenanceAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@frontend/web';

    /**
     * @var array
     */
    public $css = [
        'fonts/stylesheet.css',
        YII_ENV_PROD ? 'css/style.css' : 'css/style.less',
    ];

    /**
     * @var array
     */
    public $depends = [
        FrontendResetAsset::class,
    ];
}
