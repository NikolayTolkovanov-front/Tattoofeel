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
class FrontendJMaskAsset extends AssetBundle
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
}
