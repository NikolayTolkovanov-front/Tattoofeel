<?php

namespace common\models\traits;

use \Yii;

trait Icon
{
    public function getIconUrl() {
        $path = Yii::getAlias('@storageSource').'/'.$this->icon_path;
        $path = str_replace('//', '/', $path);
        $path = str_replace('\\', '/', $path);

        $img = str_replace('\\', '/', Yii::getAlias("@storageSourceUrl").'/'.$this->icon_path);

        return $this->icon_path && file_exists($path) ? $img : '/img/default.png';
    }
    public function hasIcon() {
        $path = Yii::getAlias('@storageSource').'/'.$this->icon_path;
        $path = str_replace('//', '/', $path);
        $path = str_replace('\\', '/', $path);

        return $this->icon_path && file_exists($path);
    }
}
