<?php

namespace frontend\modules\lk\models;

use cheatsheet\Time;
use common\commands\SendEmailCommand;
use common\models\EmailTemplate;
use common\models\UserClient;
use common\models\UserClientToken;
use frontend\modules\lk\Module;
use Yii;
use yii\base\Exception;
use yii\base\Model;
use yii\helpers\Url;

/**
 * Signup form
 */
class SignupForm extends Model
{
    /**
     * @var
     */
    public $username;
    /**
     * @var
     */
    public $email;
    public $phone;
    public $full_name;
    public $offers;
    /**
     * @var
     */
    public $password;
    public $password_confirm;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'filter', 'filter' => 'trim'],
            ['username', 'required'],
            [['full_name','phone'], 'required'],
            [['full_name','phone'], 'string'],
            ['username', 'unique',
                'targetClass' => '\common\models\UserClient',
                'message' => 'Логин уже существует'
            ],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'unique',
                'targetClass' => '\common\models\UserClient',
                'message' => 'Email уже используется'
            ],

            ['password', 'required'],
            ['password', 'string', 'min' => 6],
            [
                'password_confirm',
                'required',
                'when' => function ($model) {
                    return !empty($model->password);
                }
            ],
            ['password_confirm', 'compare', 'compareAttribute' => 'password', 'skipOnEmpty' => false],
            ['offers', 'compare', 'compareValue' => 1, 'message' => 'Вы не согласен с условиями Публичной оферты'],

        ];
    }

    public function attributeLabels()
    {
        return [
            'username' => Yii::t('frontend', 'Логин'),
            'password' => Yii::t('frontend', 'Пароль'),
            'password_confirm' => Yii::t('frontend', 'Повторить пароль'),
            'offers' => Yii::t('frontend', 'Условия соглашения'),
            'full_name' => Yii::t('frontend', 'ФИО'),
            'phone' => Yii::t('frontend', 'Телефон'),
        ];
    }

    /**
     * Signs user up.
     *
     * @return UserClient|null the saved model or null if saving fails
     * @throws Exception
     */
    public function signup()
    {
        if ($this->validate()) {
            $shouldBeActivated = $this->shouldBeActivated();
            $user = new UserClient();
            $user->username = $this->username;
            $user->email = $this->email;
            $user->status = $shouldBeActivated ? UserClient::STATUS_NOT_ACTIVE : UserClient::STATUS_ACTIVE;
            $user->setPassword($this->password);

            if (!$user->save()) {
                throw new Exception("UserClient couldn't be  saved");
            };

            $user->client_created_by = $user->id;
            $user->client_updated_by = $user->id;
            $user->save(false);

            $user->afterSignup(['full_name' => $this->full_name, 'phone' => $this->phone]);
            if ($shouldBeActivated) {
                $token = UserClientToken::create(
                    $user->id,
                    UserClientToken::TYPE_ACTIVATION,
                    168 * 3600 // 168 часов
                );

                $url = Yii::$app->formatter->asUrl(Url::to(['/lk/activation', 'token' => $token->token], true));
                $body = EmailTemplate::render(EmailTemplate::ACCOUNT_ACTIVATION_TEMPLATE, [
                    'activation_url' => $url,
                ]);

                if (empty($body)) {
                    $body = "<p>Вы успешно зарегистрированы! Для активации аккаунта перейдите по ссылке {$url}</p>";
                }

                Yii::$app->mailer->compose()
                    ->setTo($this->email)
                    ->setFrom(env('ROBOT_EMAIL'))
                    ->setSubject('Активация аккаунта')
                    ->setHtmlBody($body)
                    ->send();
            }
            return $user;
        }

        return null;
    }

    /**
     * @return bool
     */
    public function shouldBeActivated()
    {
        /** @var Module $userModule */
        $userModule = Yii::$app->getModule('lk');
        if (!$userModule) {
            return false;
        } elseif ($userModule->shouldBeActivated) {
            return true;
        } else {
            return false;
        }
    }
}
