<?php

namespace backend\widgets\form;

use trntv\yii\datetime\DateTimeWidget;

class DateTime extends DateTimeWidget
{
    public function init()
    {
        $this->options = [
            'phpDatetimeFormat' => 'yyyy-MM-dd\'T\'HH:mm:ssZZZZZ',
        ];

        parent::init();
    }
}
