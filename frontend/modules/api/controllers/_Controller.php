<?php

namespace frontend\modules\api\controllers;

use frontend\modules\api\traits\Helpers;

use yii\rest\Controller;

abstract class _Controller extends Controller
{
    use Helpers;

    public function actions()
    {
        return [
            'options' => [
                'class' => 'yii\rest\OptionsAction',
            ],
        ];
    }

    public function behaviors()
    {
        return [
            'corsFilter' => [
                'class' => \yii\filters\Cors::class,
                'cors' => [
                    'Origin' => \Yii::$app->params['origins'],
                    'Access-Control-Request-Method' => ['OPTIONS', 'GET', 'POST', 'PUT', 'DELETE'],
                    'Access-Control-Request-Headers' => ['*'],
                    'Access-Control-Allow-Credentials' => true,
                    'Access-Control-Max-Age' => 3600,
                    'Access-Control-Expose-Headers' => ['*'],
                ],
            ],
        ];
    }

    protected function success($success, $message = null, $data = null): array
    {
        $response = [
            'success' => $success,
            'message' => $message
        ];

        if ($data) {
            $response['data'] = $data;
        }

        return $response;
    }
}
