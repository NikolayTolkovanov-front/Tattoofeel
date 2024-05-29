<?php

namespace frontend\modules\lk\models;

use cheatsheet\Time;
use common\commands\SendEmailCommand;
use common\models\EmailTemplate;
use common\models\UserClient;
use common\models\UserClientToken;
use Yii;
use yii\base\Model;
use yii\helpers\Url;

/**
 * Password reset request form
 */
class PasswordResetRequestForm extends Model
{

    public $email;

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
                'filter' => ['status' => UserClient::STATUS_ACTIVE],
                'message' => 'Пользователь не найден'
            ],
        ];
    }

    public function sendEmail()
    {
        $user = UserClient::findOne([
            'status' => UserClient::STATUS_ACTIVE,
            'email' => $this->email,
        ]);

        if ($user) {
            $token = UserClientToken::create($user->id, UserClientToken::TYPE_PASSWORD_RESET, Time::SECONDS_IN_A_DAY);

            if ($user->save()) {
//                return Yii::$app->commandBus->handle(new SendEmailCommand([
//                    'to' => $this->email,
//                    'subject' => 'Сбросить пароль, сайт {name}'.Yii::$app->name,
//                    'view' => 'passwordResetToken',
//                    'params' => [
//                        'user' => $user,
//                        'token' => $token->token
//                    ]
//                ]));

                $url = Yii::$app->formatter->asUrl(Url::to(['/lk/reset', 'token' => $token->token], true));
                $body = EmailTemplate::render(EmailTemplate::RESET_PASSWORD_TEMPLATE, [
                    'reset_url' => $url,
                ]);

                if (empty($body)) {
                    $body = "<p>Чтобы сбросить пароль, перейдите по ссылке {$url}</p>";
                }

                return Yii::$app->mailer->compose()
                    ->setTo($this->email)
                    //->setTo('medvedgreez@yandex.ru')
                    ->setFrom(env('ROBOT_EMAIL'))
                    //->setReplyTo([$this->email => $this->name])
                    ->setSubject('Сброс пароля')
                    //->setTextBody($body)
                    ->setHtmlBody($body)
                    ->send();
            }
        }

        return false;
    }

    public function attributeLabels()
    {
        return [
            'email' => Yii::t('frontend', 'E-mail')
        ];
    }
}
