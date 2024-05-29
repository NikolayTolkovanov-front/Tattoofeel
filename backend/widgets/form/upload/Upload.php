<?php

namespace backend\widgets\form\upload;

use yii\web\JsExpression;

/**
 * Class Upload
 * @package common\widgets\upload
 */
class Upload extends \trntv\filekit\widget\Upload
{
    public function init()
    {

        $this->url = ['/file/storage/upload'];

        if ( empty($this->acceptFileTypes) )
            $this->acceptFileTypes = new JsExpression('/(\.|\/)(gif|jpe?g|png)$/i');

        if ( empty($this->maxFileSize) )
            $this->maxFileSize = intval( env('UPLOAD_MAX_FILE_SIZE') );

        parent::init();
    }
}
