<?php

namespace common\models\traits;

use \Yii;

trait Path
{
    public function getUrl() {
        $path = Yii::getAlias('@storageSource').$this->path;
        $path = str_replace('//', '/', $path);
        $path = str_replace('\\', '/', $path);

        $img = str_replace('\\', '/', Yii::getAlias("@storageSourceUrl").$this->path);

        return $this->path && file_exists($path) ? $img : '/img/default.png';
    }
    public function hasUrl() {
        $path = Yii::getAlias('@storageSource').$this->path;
        $path = str_replace('//', '/', $path);
        $path = str_replace('\\', '/', $path);

        return $this->path && file_exists($path);
    }
}
