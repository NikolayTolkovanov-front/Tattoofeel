<?php

namespace frontend\modules\lk\models;

use cheatsheet\Time;
use common\commands\SendEmailCommand;
use common\models\EmailTemplate;
use common\models\UserClient;
use common\models\UserClientToken;
use Yii;
use yii\base\InvalidArgumentException;
use yii\base\Model;
use yii\helpers\Url;

/**
 * Password reset form
 */
class ResendEmailForm extends Model
{
    /**
     * @var user email
     */
    public $email;

    /**
     * Creates a form model given a token.
     *
     * @param  string $token
     * @param  array $config name-value pairs that will be used to initialize the object properties
     * @throws \yii\base\InvalidArgumentException if token is empty or not valid
     */
    public function __construct($config = [])
    {
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist',
                'targetClass' => '\common\models\UserClient',
                'filter' => ['status' => UserClient::STATUS_NOT_ACTIVE],
                'message' => ' Нет пользователя, ожидающего активации с такой электронной почтой'
            ],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'email' => Yii::t('frontend', 'E-mail')
        ];
    }

    public function sendEmail()
    {
        /* @var $user UserClient */
        $user = UserClient::findOne([
            'status' => UserClient::STATUS_NOT_ACTIVE,
            'email' => $this->email,
        ]);

        if ($user) {
            $token = UserClientToken::create($user->id, UserClientToken::TYPE_ACTIVATION,
                168 * 3600 // 168 часов
            );
//            Yii::$app->commandBus->handle(new SendEmailCommand([
//                'subject' => 'Активация аккаунта',
//                'view' => 'activation',
//                'to' => $this->email,
//                'params' => [
//                    'url' => Url::to(['/lk/activation', 'token' => $token->token], true)
//                ]
//            ]));

            $url = Yii::$app->formatter->asUrl(Url::to(['/lk/activation', 'token' => $token->token], true));
            $body = EmailTemplate::render(EmailTemplate::ACCOUNT_ACTIVATION_TEMPLATE, [
                'activation_url' => $url,
            ]);

            if (empty($body)) {
                $body = "<p>Вы успешно зарегистрированы! Для активации аккаунта перейдите по ссылке {$url}</p>";
            }

            Yii::$app->mailer->compose()
                ->setTo($this->email)
                //->setTo('medvedgreez@yandex.ru')
                ->setFrom(env('ROBOT_EMAIL'))
                //->setReplyTo([$this->email => $this->name])
                ->setSubject('Активация аккаунта')
                //->setTextBody($body)
                ->setHtmlBody($body)
                ->send();

            return true;
        }

        return false;
    }
}
