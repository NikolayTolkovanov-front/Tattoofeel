<?php

namespace frontend\models;

use common\models\EmailTemplate;
use common\models\UserClientOrder;
use common\models\UserClientProfile;
use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class BuyOneClickCartForm extends Model
{
    public $name;
    public $phone;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // name, email, subject and body are required
            //[['name', 'email', 'phone', 'body'], 'required'],
            [['name', 'phone'], 'required', 'message' => 'Поле обязательно для заполнения.'],
            // email has to be a valid email address
            [['name', 'phone'], 'string'],
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
        ];
    }

    public function createOrder()
    {
        $userId = env('BUY_ONE_CLICK_USER_ID');
        $phone = $this->phone;
        if (!empty($phone)) {
            if (strpos($phone, '+') === 0) {
                $phone = preg_replace('~\D+~', '', $phone);
                $phone = '+' . $phone;
            }
            else {
                $phone = preg_replace('~\D+~', '', $phone);
            }
        }
        //echo '<pre>';print_r($phone);echo '</pre>';

        $userProfile = UserClientProfile::find()->where(['phone' => $phone])->one();
        if (!is_null($userProfile)) {
            $userId = $userProfile->id;
        } else {
            $phone = str_replace('+7', '8', $phone);
            $userProfile = UserClientProfile::find()->where(['phone' => $phone])->one();
            if (!is_null($userProfile)) {
                $userId = $userProfile->id;
            }
        }
        //echo '<pre>';print_r($userId);echo '</pre>';die();

        $order_id = isset($_COOKIE['order_id']) ? intval($_COOKIE['order_id']) : 0;
        if (!$order_id) {
            $order = new UserClientOrder();
            $order->user_id = $userId;
            $order->isCart = 0;
            $order->date = time();
            $order->comment = $this->name;
            $order->phone = $phone;

            if (!$order->save()) {
                return false;
            }

            if (!setcookie('order_id', $order->id, 0, '/')) {
                return false;
            }
        } else {
            $order = UserClientOrder::findOne($order_id);
            if (!$order) {
                return false;
            }

            $order->user_id = $userId;
            $order->isCart = 0;
            $order->date = time();
            $order->comment = $this->name;
            $order->phone = $phone;

            if (!$order->save()) {
                return false;
            }
        }

        $cart = Yii::$app->client->identity->getCart();
        if (!$cart) {
            return false;
        }

        $products = array();
        $order->places_count = 1;
        foreach ($cart->linkProducts as $pos) {
            $products[$pos->id]['id'] = $pos->id;
            $products[$pos->id]['count'] = $pos->count;

            if ($pos->product->is_oversized) {
                $order->places_count = 2;
            }
        }

        if (empty($products)) {
            return false;
        }

        $order->setProducts($products);

        $order->sum_buy = $order->getSum();

        if ((int)$order->sum_buy <= 0) {
            return false;
        }

        $ms_order = $order->msCreateOrder();
        if (!$ms_order->status) {
            return false;
        }

        //echo '<pre>';print_r($cart->linkProducts);echo '</pre>';die();
        return true;
    }

    /**
     * Sends an email to the specified email address using the information collected by this model.
     * @param  string $email the target email address
     * @return boolean whether the model passes validation
     */
//    public function contact($email)
//    {
//        if ($model = $this->validate()) {
//            $body = EmailTemplate::render(EmailTemplate::BUY_ONE_CLICK_TEMPLATE, [
//                'user_name' => $this->name,
//                'user_phone' => $this->phone,
//                'product_link' => $this->link,
//            ]);
//
//            if (empty($body)) {
//                $body = "Заявка от {$this->name} на покупку товара \"в один клик\".\nСсылка на товар: {$this->link}\nКонтактный телефон: {$this->phone}\n";
//            }
//
//            return Yii::$app->mailer->compose()
//                //->setTo($email)
//                ->setTo('operator@tattoofeel.ru')
//                //->setTo('medvedgreez@yandex.ru')
//                ->setFrom(env('ROBOT_EMAIL'))
//                //->setReplyTo([$this->email => $this->name])
//                ->setSubject('Tattoofeel: покупка в один клик')
//                //->setTextBody($body)
//                ->setHtmlBody($body)
//                ->send();
//        } else {
//            return $model->errors;
//        }
//    }
}
