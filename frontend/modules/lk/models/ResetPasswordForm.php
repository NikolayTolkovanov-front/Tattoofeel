<?php

namespace frontend\modules\lk\models;

use Yii;
use common\models\UserClientToken;
use yii\base\InvalidArgumentException;
use yii\base\Model;

/**
 * Password reset form
 */
class ResetPasswordForm extends Model
{
    /**
     * @var
     */
    public $password;

    /**
     * @var \common\models\UserClientToken
     */
    private $token;

    /**
     * Creates a form model given a token.
     *
     * @param  string $token
     * @param  array $config name-value pairs that will be used to initialize the object properties
     * @throws \yii\base\InvalidArgumentException if token is empty or not valid
     */
    public function __construct($token, $config = [])
    {
        if (empty($token) || !is_string($token)) {
            throw new InvalidArgumentException('Токен не задан');
        }
        /** @var UserToken $tokenModel */
        $this->token = UserClientToken::find()
            ->notExpired()
            ->byType(UserClientToken::TYPE_PASSWORD_RESET)
            ->byToken($token)
            ->one();

        if (!$this->token) {
            throw new InvalidArgumentException('Ошибка сброса пароля');
        }
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['password', 'required'],
            ['password', 'string', 'min' => 6],
        ];
    }

    public function attributeLabels()
    {
        return [
            'password' => Yii::t('frontend', 'Пароль'),
        ];
    }

    public function resetPassword()
    {
        $user = $this->token->user;

        $user->password = $this->password;
        if ($user->save()) {
            $this->token->delete();
        };

        return true;
    }

}
