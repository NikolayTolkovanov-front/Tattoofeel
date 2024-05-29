<?php

namespace backend\widgets\form\upload;

/**
 * Class UploadMany
 * @package common\widgets\UploadMany
 */
class UploadMany extends Upload
{
    public function init()
    {

       $this->maxNumberOfFiles = 20;
       $this->sortable = true;

        parent::init();
    }
}
