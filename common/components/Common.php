<?php

namespace common\components;

use Yii;
use yii\base\Component;

class Common extends Component
{
    public static function Debug($arr)
    {
        print '<pre>' . print_r($arr, true) . '</pre>';
    }

    public static function ImageReplace($img)
    {
        return str_replace(['.jpg','.png','.jpeg'], '.webp', $img);
    }
}