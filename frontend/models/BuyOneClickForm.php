<?php

namespace frontend\models;

use common\models\EmailTemplate;
use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class BuyOneClickForm extends Model
{
    public $name;
    public $phone;
    public $link;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // name, email, subject and body are required
            //[['name', 'email', 'phone', 'body'], 'required'],
            [['name', 'phone', 'link'], 'required', 'message' => 'Поле обязательно для заполнения.'],
            // email has to be a valid email address
            ['name', 'string'],
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Имя',
            'phone' => 'Телефон',
            'link' => 'Ссылка на товар'
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
            $body = EmailTemplate::render(EmailTemplate::BUY_ONE_CLICK_TEMPLATE, [
                'user_name' => $this->name,
                'user_phone' => $this->phone,
                'product_link' => $this->link,
            ]);

            if (empty($body)) {
                $body = "Заявка от {$this->name} на покупку товара \"в один клик\".\nСсылка на товар: {$this->link}\nКонтактный телефон: {$this->phone}\n";
            }

            return Yii::$app->mailer->compose()
                //->setTo($email)
                ->setTo('operator@tattoofeel.ru')
                //->setTo('medvedgreez@yandex.ru')
                ->setFrom(env('ROBOT_EMAIL'))
                //->setReplyTo([$this->email => $this->name])
                ->setSubject('Tattoofeel: покупка в один клик')
                //->setTextBody($body)
                ->setHtmlBody($body)
                ->send();
        } else {
            return $model->errors;
        }
    }
}
