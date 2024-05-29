<?php

namespace frontend\modules\api\controllers;

use common\models\UserClientProfile;
use frontend\models\UserClient;

class AuthController extends _Controller
{
    private function _validatePhone($phone)
    {
        if (empty($phone) || !preg_match('/^\+\d{1,3}\d{1,3}\d{6}$/i', $phone)) {
            return false;
        } else return true;
    }

    /**
     * @SWG\Get(
     *     path="/auth/send-sms/?phone={phone}",
     *     tags={"Авторизация"},
     *     summary="Отправить СМС на указанный номер.",
     *     @SWG\Parameter(
     *         in="query",
     *         name="phone",
     *         type="string",
     *         required=true,
     *         description="Номер телефона.",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Общий JSON ответ",
     *         @SWG\Schema(ref="#/definitions/Response")
     *     )
     * )
     */
    public function actionSendSms()
    {
        $phone = $this->request('phone');
        if (!$this->_validatePhone($phone)) {
            return $this->success(false,'Неверный формат телефона');
        }

        $code = rand(1000, 9999);
        $text = 'Ваш код: ' . $code;

        \Yii::$app->cache->set('auth_code_' . $code, $phone, 120);

        $success = $this->module('api')
            ->sms->send($phone, $text);

        return $this->success($success);
    }

    /**
     * @SWG\Get(
     *     path="/auth/sms-status/?phone={phone}",
     *     tags={"Авторизация"},
     *     summary="Получить статус СМС для указанного номера.",
     *     @SWG\Parameter(
     *         in="query",
     *         name="phone",
     *         type="string",
     *         required=true,
     *         description="Номер телефона.",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Общий JSON ответ",
     *         @SWG\Schema(ref="#/definitions/Response")
     *     )
     * )
     */
    public function actionSmsStatus()
    {
        $phone = $this->request('phone');
        if (!$this->_validatePhone($phone)) {
            return $this->success(false,'Неверный формат телефона');
        }

        $success = $this->module('api')
            ->sms->status($phone);

        return $this->success($success);
    }

    /**
     * @SWG\Get(
     *     path="/auth/login/?code={code}",
     *     tags={"Авторизация"},
     *     summary="Войти в аккаунт используя код СМС.",
     *     @SWG\Parameter(
     *         in="query",
     *         name="code",
     *         type="string",
     *         required=true,
     *         description="СМС код.",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Общий JSON ответ с полем <b>auth_key</b>.",
     *         @SWG\Schema(ref="#/definitions/ResponseWithData"),
     *     )
     * )
     */
    public function actionLogin()
    {
        $code = $this->request('code');
        if (empty($code)) {
            return $this->success(false,'Пустой код');
        } else if (!$phone = \Yii::$app->cache->get('auth_code_' . $code)) {
            return $this->success(false,'Неверный код');
        }

        $client = UserClientProfile::searchClientByPhone($phone);
        if ($client) { // get existing.
            $userClientProfile = UserClientProfile::find()
                ->where(['client_ms_id' => $client['client_ms_id']])
                ->one();

            // note: a user considered as active if found in MC.
            $userClient = UserClient::findOne($userClientProfile->user_id);
        } else { // create new one.
            // todo: reuse user creation api when ready
            $userClient = new UserClient();
            $userClient->status = UserClient::STATUS_ACTIVE;
            $userClient->username = 'TEST';
            $userClient->save(false);

            $userClientProfile = new UserClientProfile();
            $userClientProfile->phone = $phone;
            $userClientProfile->full_name = 'TEST';
            $userClientProfile->link('user', $userClient);
            $userClientProfile->sync();
        }

        \Yii::$app->user->enableAutoLogin = true;
        \Yii::$app->user->login($userClient, 3600);

        $userClient->processCartAfterLogin();

        $this->trigger(UserClient::EVENT_AFTER_LOGIN);

        return $this->success(true, null, [
            'auth_key' => $userClient->auth_key
        ]);
    }
}
