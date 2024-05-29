<?php

namespace common\behaviors;

use yii\base\Behavior;
use yii\web\User;

/**
 * @author Eugene Terentev <eugene@terentev.net>
 */
class LoginTimestampBehavior extends Behavior
{
    /**
     * @var string
     */
    public $attribute = 'logged_at';
    public $attribute_ip = 'logged_ip';
    public $attribute_agent = 'logged_agent';


    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            User::EVENT_AFTER_LOGIN => 'afterLogin'
        ];
    }

    /**
     * @param $event \yii\web\UserEvent
     */
    public function afterLogin($event)
    {
        $user = $event->identity;
        $user->touch($this->attribute);

        $user->{$this->attribute_ip} = \Yii::$app->request->userIP;
        $user->{$this->attribute_agent} = \Yii::$app->request->userAgent;

        $user->save(false);
    }
}
