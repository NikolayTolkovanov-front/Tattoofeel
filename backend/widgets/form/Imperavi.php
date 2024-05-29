<?php

namespace backend\widgets\form;

/**
 * Class Imperavi
 * @package common\widgets\imperavi
 */
class Imperavi extends \yii\imperavi\Widget
{

    public $height = 400;

    public function init()
    {

        $this->plugins = ['fullscreen', 'fontcolor', 'video'];
        $this->options = [
            'minHeight' => $this->height,
            'maxHeight' => $this->height,
            'buttonSource' => true,
            'convertDivs' => false,
            'removeEmptyTags' => true,
            'imageUpload' => \Yii::$app->urlManager->createUrl(['/file/storage/upload-imperavi']),
        ];

        parent::init();
    }
}
