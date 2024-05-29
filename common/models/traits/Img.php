<?php

namespace common\models\traits;

use \Yii;

trait Img
{
    public function getImgUrl() {
        $path = Yii::getAlias('@storageSource').'/'.$this->thumbnail_path;
        $path = str_replace('//', '/', $path);
        $path = str_replace('\\', '/', $path);

        $img = str_replace('\\', '/', Yii::getAlias("@storageSourceUrl").'/'.$this->thumbnail_path);

        return $this->thumbnail_path && file_exists($path) ? $img : '/img/default.png';
    }
    public function getImg2Url() {
        $path = Yii::getAlias('@storageSource').'/'.$this->thumbnail_path_2;
        $path = str_replace('//', '/', $path);
        $path = str_replace('\\', '/', $path);

        $img = str_replace('\\', '/', Yii::getAlias("@storageSourceUrl").'/'.$this->thumbnail_path_2);

        return $this->thumbnail_path && file_exists($path) ? $img : '/img/default.png';
    }
    public function hasImg() {
        $path = Yii::getAlias('@storageSource').'/'.$this->thumbnail_path;
        $path = str_replace('//', '/', $path);
        $path = str_replace('\\', '/', $path);

        return $this->thumbnail_path && file_exists($path);
    }
    public function hasImg2() {
        $path = Yii::getAlias('@storageSource').'/'.$this->thumbnail_path_2;
        $path = str_replace('//', '/', $path);
        $path = str_replace('\\', '/', $path);

        return $this->thumbnail_path_2 && file_exists($path);
    }
}
