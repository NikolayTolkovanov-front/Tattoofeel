<?php

namespace backend\models;

use cheatsheet\Time;
use common\models\AdminIp;
use common\models\User;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;

/**
 * Login form
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;

    private $user = false;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'username' => Yii::t('backend', 'Username'),
            'password' => Yii::t('backend', 'Password'),
            'rememberMe' => Yii::t('backend', 'Remember Me')
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
                $this->addError('password', Yii::t('backend', 'Incorrect username or password.'));
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
            $this->user = User::find()
                ->andWhere(['or', ['username' => $this->username], ['email' => $this->username]])
                ->one();
        }

        return $this->user;
    }

    /**
     * Logs in a user using the provided username and password.
     * @return bool whether the user is logged in successfully
     * @throws ForbiddenHttpException
     */
    public function login()
    {
        if (!$this->validate()) {
            return false;
        }

        $duration = $this->rememberMe ? Time::SECONDS_IN_A_MONTH : 0;
        Yii::info("logon duration set to ". $duration);

        if (Yii::$app->user->login($this->getUser(), $duration)) {
            if (!Yii::$app->user->can('loginToBackend')) {
                Yii::$app->user->logout();
                throw new ForbiddenHttpException;
            }

            // Проверка по списку разрешенных IP адресов (если список пуст, то разрешено всем)
            $arIP = ArrayHelper::map(AdminIp::find()->all(), 'id', 'ip_address');
            if (!empty($arIP) && !in_array(Yii::$app->request->userIP, $arIP)) {
                Yii::$app->user->logout();
                throw new ForbiddenHttpException;
            }
            
            return true;
        }

        return false;
    }
}
