<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace backend\assets;

use common\assets\AdminLte;
use common\assets\Html5shiv;
use yii\web\AssetBundle;
use yii\web\YiiAsset;

class BackendJMaskAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@bower';

    /**
     * @var array
     */
    public $js = [
        'jquery.inputmask/dist/jquery.inputmask.min.js',
        'jquery.inputmask/dist/bindings/inputmask.binding.js',
    ];

    /**
     * @var array
     */
    public $depends = [
        YiiAsset::class
    ];
}
