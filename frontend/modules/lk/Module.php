<?php

namespace frontend\modules\lk;

class Module extends \yii\base\Module
{
    /**
     * @var string
     */
    public $controllerNamespace = 'frontend\modules\lk\controllers';

    /**
     * @var bool Is users should be activated by email
     */
    public $shouldBeActivated = true;
    /**
     * @var bool Enables login by pass from backend
     */
    public $enableLoginByPass = false;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
    }
}
