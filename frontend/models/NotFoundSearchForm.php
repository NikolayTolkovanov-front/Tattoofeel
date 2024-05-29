<?php

namespace frontend\models;

use common\models\EmailTemplate;
use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class NotFoundSearchForm extends Model
{
    public $name;
    //public $email;
    public $phone;
    public $product;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // name, email, subject and body are required
            //[['name', 'email', 'phone', 'body'], 'required'],
            [['name', 'phone', 'product'], 'required', 'message' => 'Поле обязательно для заполнения.'],
            // email has to be a valid email address
            [['name', 'product'], 'string'],
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
            'product' => 'Название товара'
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
//                ->setSubject('Tattoofeel: не нашли, что искали')
//                ->setTextBody("Обратная связь из формы \"Не нашли, что искали\" от {$this->name}\nКонтактный телефон: {$this->phone}\nНазвание товара: {$this->product}")
//                ->send();

            $body = EmailTemplate::render(EmailTemplate::NOT_FOUND_SEARCH_TEMPLATE, [
                'user_name' => $this->name,
                'user_phone' => $this->phone,
                'product_name' => $this->product,
            ]);

            if (empty($body)) {
                $body = "Обратная связь из формы \"Не нашли, что искали\" от {$this->name}\nКонтактный телефон: {$this->phone}\nНазвание товара: {$this->product}\n";
            }

            return Yii::$app->mailer->compose()
                ->setTo($email)
                //->setTo('medvedgreez@yandex.ru')
                ->setFrom(env('ROBOT_EMAIL'))
                //->setReplyTo([$this->email => $this->name])
                ->setSubject('Tattoofeel: не нашли, что искали')
                //->setTextBody($body)
                ->setHtmlBody($body)
                ->send();
        } else {
            return $model->errors;
            //return false;
        }
    }
}
