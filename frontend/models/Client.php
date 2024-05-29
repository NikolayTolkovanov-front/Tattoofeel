<?php

namespace frontend\models;

use Yii;

class Client
{
    public $identity = null;
    public function __construct()
    {
        $this->identity = Yii::$app->user->isGuest ? new UserGuestClient() : Yii::$app->user->identity;
    }
}
