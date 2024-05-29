<?php

namespace frontend\models;

use common\models\EmailTemplate;
use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class ReviewsForm extends Model
{
    public $name;
    //public $email;
    public $phone;
    public $body;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // name, email, subject and body are required
            //[['name', 'email', 'phone', 'body'], 'required'],
            [['name', 'phone', 'body'], 'required', 'message' => 'Поле обязательно для заполнения.'],
            // email has to be a valid email address
            [['name', 'body'], 'string'],
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Имя',
            //'email' => 'Email',
            'phone' => 'Телефон',
            'body' => 'Отзыв'
        ];
    }

    /**
     * Sends an email to the specified email address using the information collected by this model.
     * @param  string $email the target email address
     * @return boolean whether the model passes validation
     */
    public function contact($email)
    {
        if ($model = $this->validate()) {
//            return Yii::$app->mailer->compose()
//                ->setTo($email)
//                //->setTo('medvedgreez@yandex.ru')
//                ->setFrom(env('ROBOT_EMAIL'))
//                //->setReplyTo([$this->email => $this->name])
//                ->setSubject('Tattoofeel: предложения и отзывы')
//                ->setTextBody("Поступил новый отзыв от {$this->name}\nКонтактный телефон: {$this->phone}\nТекст отзыва: {$this->body}")
//                ->send();

            $body = EmailTemplate::render(EmailTemplate::REVIEWS_TEMPLATE, [
                'user_name' => $this->name,
                'user_phone' => $this->phone,
                'user_text' => $this->body,
            ]);

            if (empty($body)) {
                $body = "Поступил новый отзыв от {$this->name}\nКонтактный телефон: {$this->phone}\nТекст отзыва: {$this->body}\n";
            }

            return Yii::$app->mailer->compose()
                ->setTo($email)
                //->setTo('medvedgreez@yandex.ru')
                ->setFrom(env('ROBOT_EMAIL'))
                //->setReplyTo([$this->email => $this->name])
                ->setSubject('Tattoofeel: предложения и отзывы')
                //->setTextBody($body)
                ->setHtmlBody($body)
                ->send();
        } else {
            return $model->errors;
            //return false;
        }
    }
}
