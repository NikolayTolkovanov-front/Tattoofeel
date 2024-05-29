<?php

namespace api\errors;

use yii\web\HttpException;

class ErrorMsg extends \Exception
{

    public static function customErrorMsg($error_code, $message = null, $code = 0, \Exception $previous = null, $extra_content = null)
    {
        $httpException = new HttpException($error_code, $message, $code, $previous);
        \Yii::$app->response->statusCode = $error_code;
        $custom_err = array(
            'name' => $httpException->getName(),
            'message' => $message,
            'code' => $code,
            'extraContent' => $extra_content,
            'status' => $error_code
        );
        return $custom_err;
    }
}