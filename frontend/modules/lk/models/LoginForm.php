<?php

namespace frontend\modules\lk\models;

use cheatsheet\Time;
use frontend\models\UserClient;
use Yii;
use yii\base\Model;

/**
 * Login form
 */
class LoginForm extends Model
{
    public $identity;
    public $password;
    public $rememberMe = false;

    private $user = false;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['identity', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'identity' => Yii::t('frontend', 'Логин или email'),
            'password' => Yii::t('frontend', 'Пароль'),
            'rememberMe' => Yii::t('frontend', 'Запомнить'),
        ];
    }


    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     */
    public function validatePassword()
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                if (UserClient::find()
                    ->notActive()
                    ->andWhere(['or', ['username' => $this->identity], ['email' => $this->identity]])
                    ->one()) {
                    $this->addError('password', 'Необходимо подтвержение указанной при регистрации почты. Если письма - нет, проверьте папку "спам"');
                } else {
                    $this->addError('password', 'Неправильно введен Логин или Пароль');
                }
            }
        }
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->user === false) {
            $this->user = UserClient::find()
                ->active()
                ->andWhere(['or', ['username' => $this->identity], ['email' => $this->identity]])
                ->one();
        }

        return $this->user;
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        //Yii::info("login action: " . Yii::$app->request->referrer);
        //Yii::info("remember: " . $this->rememberMe);
//        Yii::$app->request->referrer = "https://tattoofeel.ru/";
        if ($this->validate()) {
//            Yii::$app->user->enableSession = true;
//            if ($this->rememberMe)
//                Yii::$app->user->enableAutoLogin = true;
            $duration = $this->rememberMe ? Time::SECONDS_IN_A_MONTH : 0;
            //Yii::info("logon duration set to ". $duration);
            if (Yii::$app->user->login($this->getUser(), $duration)) {
                $this->getUser()->afterLogin();
                //Yii::info("test login true");

                return true;
            }
        }
        //Yii::info("test login false");

        return false;
    }
}
