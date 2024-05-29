<?php

namespace frontend\modules\api;

use frontend\modules\api\components\ProstorSMS;

use yii\web\Response;

/**
 * frontAPI module definition class
 */
class Module extends \yii\base\Module
{
    public $controllerNamespace = 'frontend\modules\api\controllers';

    public function init()
    {
        parent::init();

        \Yii::$app->log->targets[] = \Yii::createObject([
            'class' => 'yii\log\FileTarget',
            'categories' => ['sms'],
            'logFile' => '@app/runtime/logs/sms.log',
            'logVars' => []
        ]);

        \Yii::$app->set('response', [
            'class' => Response::class,
            'on beforeSend' => function ($event) {
                /* @var $response Response */
                $response = $event->sender;
                if (\Yii::$app->controller->id != 'default') {
                    $response->format = Response::FORMAT_JSON;
                    if (!$response->isSuccessful) {
                        $message = $response->data['name'] ?? 'Что-то пошло не так!';
                        if (!empty($response->data['message'])) {
                            $message = $response->data['message'];
                        }
                        $response->data = [
                            'success' => false,
                            'message' => $message
                        ];
                    }
                }
            },
        ]);

        $this->components = [
            'sms' => [
                'class' => ProstorSMS::class,
                'username' => env('PROSTOR_SMS_USER'),
                'password' => env('PROSTOR_SMS_PASS')
            ]
        ];
    }
}
