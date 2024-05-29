<?php

namespace frontend\modules\lk\controllers;

use common\components\cdek\CalculateTariffList;
use common\models\BankCards;
use common\models\Commission;
use common\models\Coupons;
use common\models\DeliveryCity;
use common\models\SdekPvz;
use common\models\UserClient;
use common\models\UserClientOrder;
use common\models\UserClientOrder_Product;
use common\models\UserClientProfile;
use common\models\UserClientToken;
use common\models\PaymentTypes;
use common\traits\FormAjaxValidationTrait;
use frontend\controllers\BaseController;
use frontend\models\Product;
use frontend\modules\lk\models\Delivery;
use frontend\modules\lk\models\PasswordResetRequestForm;
use frontend\modules\lk\models\ResendEmailForm;
use frontend\modules\lk\models\ResetPasswordForm;
use frontend\modules\lk\models\SearchForm;
use frontend\modules\lk\models\SignupForm;
use frontend\helpers\Debug as _;

use yii\base\InvalidArgumentException;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;

use Yii;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use frontend\modules\lk\models\LoginForm;
use yii\web\Response;

class DefaultController extends BaseController
{
    use FormAjaxValidationTrait;

    const DELIVERY_CDEK = 'cdek';
    const DELIVERY_IML = 'iml';
    const DELIVERY_PICK_POINT = 'pick_point';

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => [
                            'register',
                            'login',
                            'cart',
                            'deferred',
                            'remember',
                            'resend',
                            'activation',
                            'reset',
                            'get-cart-list',
                            'pay-success'
                        ],
                        'allow' => true,
                        'roles' => ['?']
                    ],
                    [
                        'actions' => [
                            'pay',
                        ],
                        'allow' => false,
                        'roles' => ['?']
                    ],
                    [
                        'actions' => [
                            'register',
                            'login',
                            'remember',
                            'resend',
                            'activation',
                            'reset'
                        ],
                        'allow' => false,
                        'roles' => ['@'],
                        'denyCallback' => function () {
                            return Yii::$app->controller->redirect(['/lk']);
                        }
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@']
                    ],
                ]
            ]
        ];
    }

    private function endsWith($haystack, $needle)
    {
        if (empty($haystack)) {
            return false;
        }
        $length = strlen($needle);
        return $length > 0 ? substr($haystack, -$length) === $needle : true;
    }

    public function actionLogin()
    {
        $model = new LoginForm();

        $post = Yii::$app->request->post();

        if (empty($post)) {
            Url::remember(Yii::$app->request->referrer, 'prev');
        }

        if ($model->load($post) && $model->login()) {
            $url = Url::previous('prev');
            if (empty($url) || $this->endsWith($url, "/login")) {
                $url = "https://tattoofeel.ru/lk/";
            }
            return $this->redirect($url);
        };

        return $this->render('login', [
            'productsRecently' => new ActiveDataProvider([
                'query' => $this->popularQuery(),
                'pagination' => false,
            ]),
            'model' => $model
        ]);
    }

    /**
     * @return string|Response
     * @throws \yii\base\Exception
     */
    public function actionRegister()
    {

        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            $user = $model->signup();
            if ($user) {
                if ($model->shouldBeActivated()) {
                    Yii::$app->getSession()->setFlash('alert', [
                        'body' => Yii::t(
                            'frontend',
                            'Ваша учетная запись была успешно создана. Проверьте свою электронную почту для получения дальнейших инструкций'
                        ),
                        'options' => ['class' => 'alert-success']
                    ]);
                } else {
                    Yii::$app->getUser()->login($user);
                }
                return $this->redirect(['/lk']);
            }
        }

        return $this->render('register', [
            'model' => $model,
            'productsRecently' => new ActiveDataProvider([
                'query' => $this->popularQuery(),
                'pagination' => false,
            ])
        ]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * @return string|Response
     * @throws \yii\base\ExitException
     */
    public function actionIndex()
    {
        $profile = Yii::$app->user->identity->userProfile;

        $this->performAjaxValidation($profile);

        if ($profile->load(Yii::$app->request->post()) && $profile->save()) {
            return $this->redirect(['/lk']);
        }

        return $this->render('index', [
            'profile' => $profile,
            'productsRecently' => new ActiveDataProvider([
                'query' => $this->popularQuery(),
                'pagination' => false,
            ])
        ]);
    }

    public function actionCart()
    {
        if (Yii::$app->user->isGuest)
            $cart = null;
        else
            $cart = Yii::$app->client->identity->getCart();

        $coupon = null;
        if ($cart && $cart->coupon_id) {
            $coupon = Coupons::findOne($cart->coupon_id);
        }

        $cartInfoMessage = UserClientOrder::getCartInfoMessage($cart);

        return $this->render('cart', [
            'cartInfoMessage' => $cartInfoMessage,
            'model' => $cart,
            'cart' => $cart ?? Yii::$app->client->identity->getCart(),
            'coupon' => $coupon,
            'paymentTypes' => PaymentTypes::find()->where(['active' => true])->all(),
            'productsRecently' => new ActiveDataProvider([
                'query' => $this->popularQuery(),
                'pagination' => false,
            ])
        ]);
    }

    public function actionDeferred()
    {
        return $this->render('deferred', [
            'deferred' => Yii::$app->client->identity->deferred,
            'productsRecently' => new ActiveDataProvider([
                'query' => $this->popularQuery(),
                'pagination' => false,
            ])
        ]);
    }

    public function actionOrders()
    {
        return $this->render('orders', [
            'orders' => Yii::$app->client->identity->orders,
            'productsRecently' => new ActiveDataProvider([
                'query' => $this->popularQuery(),
                'pagination' => false,
            ])
        ]);
    }

    public function actionChangeOrderProducts()
    {
        if (!Yii::$app->request->isAjax) {
            throw new NotFoundHttpException();
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        $order_id = Yii::$app->request->post('orderId');
        $products = Yii::$app->request->post('products');

        if ($order_id < 1 || empty($products)) {
            return ['status' => 'error', 'error' => 'Неверные параметры запроса'];
        }

        $order = UserClientOrder::find()->where(['id' => $order_id])->one();

        if (!$order || $order->user_id != Yii::$app->client->identity->id) {
            throw new NotFoundHttpException();
        }

        if ($order->status_pay) {
            return ['status' => 'error', 'error' => 'Заказ уже оплачен'];
        }

        $backup_products = UserClientOrder_Product::find()->where(['order_id' => $order->id])->all();
        $arBackupProducts = array();
        if ($backup_products) {
            foreach ($backup_products as $product) {
                $arBackupProducts[$product->id] = array(
                    'id' => $product->id,
                    'count' => $product->count,
                );
            }
        }

        $arProducts = array();
        foreach ($products as $product) {
            $arProducts[$product['id']] = array(
                'id' => $product['id'],
                'count' => $product['quantity'],
            );
        }

        if (!empty($arProducts)) {
            $order->setProducts($arProducts);
            $order->sum_buy = $order->getSum();
            $order->save(false);

            $result = $order->msChangeOrderProducts();

            if ($result->status) {
                return [
                    'status' => 'ok',
                    'orderId' => $order->id,
                    'orderSum' => $order->getTotalSumFormat(),
                    'order' => $order,
                ];
            } else {
                if (!empty($arBackupProducts)) {
                    $order->setProducts($arBackupProducts);
                }
                return ['status' => 'error', 'error' => $result->msg];
            }
        }

        if (!empty($arBackupProducts)) {
            $order->setProducts($arBackupProducts);
        }
        return ['status' => 'error', 'error' => 'Ошибка во время выполнения запроса'];
    }

    public function actionLoginAs()
    {
        if (!(int)Yii::$app->getUser()->identity->is_manager) {
            throw new HttpException(403, 'Доступ запрещен');
        }

        $search = new SearchForm();

        if ($search->load(Yii::$app->request->get()) && $search->validate()) {

            $users = array();
            if (!empty($search->full_name) || !empty($search->phone) || !empty($search->email)) {
                $users = UserClient::find()
                    ->innerJoin(UserClientProfile::tableName(), UserClientProfile::tableName() . ".user_id = " . UserClient::tableName() . ".id")
                    ->andWhere([UserClient::tableName() . ".status" => UserClient::STATUS_ACTIVE]);

                if (!empty($search->full_name)) {
                    $users = $users->andFilterWhere(['like', UserClientProfile::tableName() . ".full_name", $search->full_name]);
                }

                if (!empty($search->phone)) {
                    $users = $users->andFilterWhere(['like', UserClientProfile::tableName() . ".phone", $search->phone]);
                }

                if (!empty($search->email)) {
                    $users = $users->andFilterWhere(['like', UserClient::tableName() . ".email", $search->email]);
                }

                $users = $users->all();
            }

            return $this->render('login-as', [
                'search' => $search,
                'users' => $users,
                'productsRecently' => new ActiveDataProvider([
                    'query' => $this->popularQuery(),
                    'pagination' => false,
                ])
            ]);
        }

        return $this->render('login-as', [
            'search' => $search,
            'productsRecently' => new ActiveDataProvider([
                'query' => $this->popularQuery(),
                'pagination' => false,
            ])
        ]);
    }

    public function actionLoginById()
    {
        if (!(int)Yii::$app->getUser()->identity->is_manager) {
            throw new HttpException(403, 'Доступ запрещен');
        }

        $request = Yii::$app->request;
        $userId = $request->post('user_id');

        if ($request->isPost && 0 < (int)$userId) {
            $identity = UserClient::findOne($userId);

            if ($identity) {
                Yii::$app->user->login($identity);
            }
        }

        return $this->redirect(['/lk']);
    }

    public function actionTest()
    {
        if ((int)Yii::$app->getUser()->identity->id !== 27) {
            throw new HttpException(403, 'Доступ запрещен');
        }

        $model = Yii::$app->client->identity->getCart();

        $model = UserClientOrder::find()->where(['order_ms_id' => 'b0388c18-45de-11eb-0a80-0851003ad7b3'])->one();


        return $this->render('test', [
            'resp' => $model
        ]);
    }

    /**
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionGetCartList()
    {

        if (!Yii::$app->request->isAjax) {
            throw new NotFoundHttpException();
        }

        return $this->renderPartial('_cart_list', [
            'cart' => Yii::$app->client->identity->getCart()
        ]);
    }

    public function actionGetOrderItem($id, $count = 1)
    {
        if (!Yii::$app->request->isAjax) {
            throw new NotFoundHttpException();
        }

        $p = Product::findOne($id);

        if (!$p || $p->amount - $count < $count) {
            return '';
        }

        $lp = new UserClientOrder_Product();
        $lp->product_id = $id;
        $lp->count = 1;

        return $this->renderPartial('_order_item', [
            'lp' => $lp,
        ]);
    }

    protected function popularQuery()
    {
        return Product::find()->popular()->limit(16);
    }

    /**
     * @param $token
     * @return Response
     * @throws HttpException
     * @throws \Exception
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionActivation($token)
    {
        if (!$token) {
            throw new HttpException(400, 'Незадан токен активации');
        }

        $token = UserClientToken::find()
            ->byType(UserClientToken::TYPE_ACTIVATION)
            ->byToken($token)
            ->one();

        if (!$token) {
            throw new HttpException(401, 'Неверный токен активации');
        }

        if ($token->expire_at <= time()) {
            throw new HttpException(401, 'Токен устарел, запросите повторную активацию');
        }

        $user = $token->user;
        $user->updateAttributes([
            'status' => UserClient::STATUS_ACTIVE
        ]);
        $token->delete();
        Yii::$app->getUser()->login($user);
        Yii::$app->getSession()->setFlash('alert', [
            'body' => 'Ваша учетная запись была успешно активирована',
            'options' => ['class' => 'alert-success']
        ]);

        return $this->redirect(['/lk']);
    }

    /**
     * @return string|Response
     */
    public function actionRemember()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->getSession()->setFlash('alert', [
                    'body' => Yii::t('frontend',
                        'Проверьте свою электронную почту для получения дальнейших инструкций.'),
                    'options' => ['class' => 'alert-success']
                ]);

                return $this->redirect(['/lk']);
            } else {
                Yii::$app->getSession()->setFlash('alert', [
                    'body' => Yii::t('frontend',
                        'К сожалению, мы не можем сбросить пароль для предоставленной электронной почты.'),
                    'options' => ['class' => 'alert-danger']
                ]);
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
            'productsRecently' => new ActiveDataProvider([
                'query' => $this->popularQuery(),
                'pagination' => false,
            ])
        ]);
    }

    /**
     * @param $token
     * @return string|Response
     * @throws BadRequestHttpException
     */
    public function actionReset($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->getSession()->setFlash('alert', [
                'body' => 'Новый пароль сохранен',
                'options' => ['class' => 'alert-success']
            ]);
            return $this->redirect(['/lk']);
        }

        return $this->render('resetPassword', [
            'model' => $model,
            'productsRecently' => new ActiveDataProvider([
                'query' => $this->popularQuery(),
                'pagination' => false,
            ])
        ]);
    }

    public function actionResend()
    {
        $model = new ResendEmailForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->getSession()->setFlash('alert', [
                    'body' => Yii::t('frontend',
                        'Проверьте свою электронную почту для получения дальнейших инструкций.'),
                    'options' => ['class' => 'alert-success']
                ]);

                return $this->redirect(['/lk']);
            } else {
                Yii::$app->getSession()->setFlash('alert', [
                    'body' => Yii::t('frontend',
                        'К сожалению, мы не можем повторно отправить ссылку для активации по электронной почте.'),
                    'options' => ['class' => 'alert-danger']
                ]);
            }
        }

        return $this->render('resend-email', [
            'model' => $model,
            'productsRecently' => new ActiveDataProvider([
                'query' => $this->popularQuery(),
                'pagination' => false,
            ])
        ]);
    }

    public function actionCdekPvz($city_code = 44)
    {
        $json = array();
        if ((int)$city_code) {
            $sdekPvz = SdekPvz::find()->where(['sdek_id' => (int)$city_code])->all();

            if (is_array($sdekPvz)) {
                foreach ($sdekPvz as $item) {
                    $json[] = unserialize($item->xml);
                }
            }
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $json;
    }

    /**
     * @param $city_id
     * @return array
     */
    public function actionCdekSumm($city_id)
    {

        $goods = [];

        $delivery = new Delivery();

        /** @var UserClientOrder $cart */
        $cart = Yii::$app->client->identity->getCart();

        foreach ($cart->linkProducts as $l) {
            //К габаритам прибавляем 10%
            $length = $l->product->length + ($l->product->length * 0.1);
            $width = $l->product->width + ($l->product->width * 0.1);
            $height = $l->product->height + ($l->product->height * 0.1);

            for ($i = 0; $i < $l->count; $i++) {
                $goods[] = [
                    'weight' => (float)!empty($l->product->weight) ? $l->product->weight : 0.1,
                    'length' => (int)!empty($l->product->length) ? $length : 10,
                    'width' => (int)!empty($l->product->width) ? $width : 10,
                    'height' => (int)!empty($l->product->height) ? $height : 10
                ];
            }
        }

        $storage_to_door_tariff_list = array_keys($delivery->getCdekTariffList($delivery::CDEK_SD_DELIVERY_TYPE));
        $storage_to_storage_tariff_list = array_keys($delivery->getCdekTariffList($delivery::CDEK_SS_DELIVERY_TYPE));

        $tariff_list = array_merge($storage_to_door_tariff_list, $storage_to_storage_tariff_list);

        $api = new CalculateTariffList([
            'receiverCityId' => $city_id,
            'tariffList' => $tariff_list,
            'goods' => $goods
        ]);

        $result = $api->calculate();

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $result;
    }

    public function actionGetCityCodes()
    {
        $cdek_city_id = $_GET['cdek_city_id'];
        $cdek_city_name = $_GET['cdek_city_name'];
        $cdek_city_id = [

            'status' => true,

            'data' => $cdek_city_id

        ];

        $deliveryz = new Delivery();

        $iml_city_code = $deliveryz->getImlCityCodeByCityName($cdek_city_name);

        $pp_city_code = $deliveryz->getPpCityCodeByCityName($cdek_city_name);

        $city_codes = [

            self::DELIVERY_CDEK => $cdek_city_id,

            self::DELIVERY_IML => $iml_city_code,

            self::DELIVERY_PICK_POINT => $pp_city_code,

        ];

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $city_codes;
    }

    public function actionPaySuccess()
    {
        //todo check repeat order double
        if (!Yii::$app->user->isGuest) {
            $model = Yii::$app->client->identity->getCart();
        } else {
            $model = UserClientOrder::findOne($_COOKIE['order_id']);

            if (!$model) {
                throw new NotFoundHttpException();
            }

            unset($_COOKIE['order_id']);
            unset($_COOKIE['cart']);
            setcookie('order_id', null, -1, '/');
            setcookie('cart', null, -1, '/');
        }

        $model->scenario = UserClientOrder::SCENARIO_PAY;
        $model->date_pay = time();
        $model->isCart = 0;

        //todo pay_id
        $model->pay_id = 'skip pay';
        $model->save(false);
        $model->msEndOrder();

        return $this->render('pay-success', [
            'productsRecently' => new ActiveDataProvider([
                'query' => $this->popularQuery(),
                'pagination' => false,
            ]),
            'order' => $model,
        ]);
    }

    public function actionPayFail()
    {
        return $this->render('pay-fail', [
            'productsRecently' => new ActiveDataProvider([
                'query' => $this->popularQuery(),
                'pagination' => false,
            ])
        ]);
    }

    public function actionPay()
    {
        $get = Yii::$app->request->get();
        if (isset($get['order_id']) && (int)$get['order_id'] > 0) {
            $order = UserClientOrder::find()->where(['id' => $get['order_id']])->one();
        } else {
            $order = Yii::$app->client->identity->getCart(); // заказ
        }

        if ($order) {
            if ($order->user_id != Yii::$app->client->identity->id) {
                throw new HttpException('404', 'Заказ не найден');
            }

            return $this->render('pay', [
                'order' => $order,
            ]);
        } else {
            return $this->redirect(['/lk']);
        }
    }

    public function actionPayCard2Card()
    {
        $get = Yii::$app->request->get();
        if (isset($get['order_id']) && (int)$get['order_id'] > 0) {
            $order = UserClientOrder::find()->where(['id' => $get['order_id']])->one();
        } else {
            $order = Yii::$app->client->identity->getCart(); // заказ
        }

        if ($order) {
            if ($order->user_id != Yii::$app->client->identity->id) {
                throw new HttpException('404', 'Заказ не найден');
            }

            return $this->render('pay-card-2-card', [
                'order' => $order,
                'card' => BankCards::find()->where(['is_actual' => 1])->one(),
            ]);
        } else {
            return $this->redirect(['/lk']);
        }
    }

    public function actionPayedCard2Card()
    {
        $get = Yii::$app->request->get();
        if (isset($get['order_id']) && (int)$get['order_id'] > 0) {
            $order = UserClientOrder::find()->where(['id' => $get['order_id']])->one();
        }

        if ($order) {
            if ($order->user_id != Yii::$app->client->identity->id) {
                throw new HttpException('404', 'Заказ не найден');
            }

            $order->status = UserClientOrder::ORDER_STATUS_NEW_PAYED;
            $order->date_pay = time();

            $order->save(false);

            $order->msSetOrderStatus(UserClientOrder::ORDER_STATUS_NEW_PAYED);

            return $this->redirect(['/lk/orders']);
        } else {
            throw new HttpException('404', 'Заказ не найден');
        }
    }

    /**
     * Оформление заказа
     * @return Response
     * @throws HttpException
     */
    public function actionOrderCheckout()
    {
        _::step('cart', __FUNCTION__, '=====');
        _::value('cart', __FUNCTION__, 'isGuest', Yii::$app->user->isGuest);

        if (!Yii::$app->user->isGuest)
            $model = Yii::$app->client->identity->getCart();
        else $model = null;

        if (!$model) {
            throw new HttpException('404', 'Заказ не найден');
        }

        _::value('cart', __FUNCTION__, 'order_id', $model->id);

        $cartInfoMessage = UserClientOrder::getCartInfoMessage($model);
        if (isset($model->order_ms_id)) {
            $model->order_ms_id = null;
            $model->date = 'now'; // omg it actually has filter applying strtotime to this sh*t
            $model->save();
        }

        $post = Yii::$app->request->post();
        _::value('cart', __FUNCTION__, 'post', $post);

        if ($model->getSum() <= 0) {
            return $this->redirect(['/lk/cart']);
        }
        $load_flag = $model->load($post);
        $validate_flag = $model->validate();

        if ($load_flag && $validate_flag && !$cartInfoMessage) {
            _::step('cart', __FUNCTION__, 'process form data');

            $model->scenario = $model::SCENARIO_ORDER_REGISTER;
            $model->setDeliveryInfo();

            $model->places_count = 1;
            foreach ($model->linkProducts as $product) {
                if ($product->product->is_oversized) {
                    $model->places_count = 2;
                    break;
                }
            }

            $model->date = time();
            $model->sum_buy = $model->getSum();
            if (is_null($model->sum_buy) || $model->sum_buy == 0) {
                return $this->redirect(['/lk/cart/?order_error=Невозможно оформить заказ. Возможно какого-то товара нет в наличии']);
            }

            _::value('cart', __FUNCTION__, 'sum_buy', $model->sum_buy);

            $commission_percent = $model->commissionPercent;
            if ($commission_percent) {
                $model->commission = $commission_percent;
            }

            _::step('cart', __FUNCTION__, 'create MC order');
            $ms_order = $model->msCreateOrder();
            _::value('cart', __FUNCTION__, 'order_ms_id', $model->order_ms_id);

            if ($ms_order->status) {
                if ($model->coupon_id) {
                    _::step('cart', __FUNCTION__, 'check coupon');
                    $coupon = Coupons::findOne($model->coupon_id);
                    _::value('cart', __FUNCTION__, 'coupon_id', $model->coupon_id);
                    if ($coupon) {
                        $coupon->used_count += 1;
                        $coupon->save(false);
                        _::step('cart', __FUNCTION__, 'coupon used');

                        if ($coupon->is_one_user) {
                            $coupon->link('userClients', Yii::$app->client->identity);
                        }
                    }
                }

                _::step('cart', __FUNCTION__, 'action ' . $post['action']);

                if (isset($post['action'])) {
                    if ($post['action'] == 'card_now') {
                        return $this->redirect(['/lk/pay']);
                    } elseif ($post['action'] == 'card2card_now') {
                        $model->isCart = 0;
                        $model->pay_id = 'card2card pay';
                        $model->save(false);
                        $model->msEndOrder();

                        return $this->redirect(['/lk/pay-card-2-card/?order_id=' . $model->id . '&tx=']);
                    }
                }

                _::step('cart', __FUNCTION__, 'payment success');

                return $this->redirect(['/lk/pay-success']);
            } else {
                _::step('cart', __FUNCTION__, 'failed');
                _::value('cart', __FUNCTION__, 'MC order status', $ms_order->statusCode ?? '');
                _::value('cart', __FUNCTION__, 'MC order message', $ms_order->msg ?? '');

                if ($ms_order->statusCode < 0) {
                    if ($ms_order->statusCode == -4) {
                        return $this->redirect(['/lk/cart/?order_error=' . base64_encode(json_encode($ms_order))]);
                    }

                    Yii::$app->getSession()->setFlash('alert', [
                        'body' => $ms_order->msg,
                        'options' => ['class' => 'alert-success']
                    ]);
                }

                return $this->redirect(['/lk/order-checkout']);
            }
        }

        _::step('cart', __FUNCTION__, 'delivery');

        $delivery = new Delivery();

        $delivery_city = UserClientOrder::find()
            ->where(['not', ['delivery_city' => null]])
            ->andWhere(['user_id' => Yii::$app->client->identity->id])
            ->orderBy(['id' => SORT_DESC])
            ->one();

        if ($delivery_city) {
            $delivery_city = $delivery_city->delivery_city;
            $delivery_city_id = DeliveryCity::find()
                ->where(['city_full' => $delivery_city])
                ->orderBy(['id' => SORT_DESC])
                ->one();
            if ($delivery_city_id) {
                $delivery_city_id = $delivery_city_id->sdek_id;
            } else {
                $delivery_city = null;
                $delivery_city_id = null;
            }
        } else {
            $delivery_city = null;
        }

        $deliveryz = new Delivery();
        if ($delivery_city_id) {
            $cdek_city_id = [
                'status' => true,
                'data' => $delivery_city_id
            ];
        } else {
            $cdek_city_id = $deliveryz->getCdekCityCodeByCityName($delivery_city);
        }

        $city_codes = [
            self::DELIVERY_CDEK => $cdek_city_id,
        ];

        _::step('cart', __FUNCTION__, 'render form');

        return $this->render('order_register', [
            'cart' => $model,
            'cartInfoMessage' => $cartInfoMessage,
            'coupon' => Coupons::findOne($model->coupon_id),
            'delivery' => $delivery,
            'paymentTypes' => PaymentTypes::find()->where(['active' => true])->all(),
            'delivery_city' => $delivery_city,
            'delivery_city_id' => $delivery_city_id,
            'city_codes' => $city_codes,
        ]);
    }

    /**
     * Получает города для выбоанного сервиса доставки, а также список сервисов доставки в
     * зоне действия которых найден искомый город ($term)
     * @param int $ds Сервис доставки
     * @param string $term Строка для поиска города
     * @return array|string
     */
    public function actionGetCities($term, $ds = Delivery::DELIVERY_CDEK)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $dModel = new Delivery();

        $delivery_list = $dModel->getDsByTerm($term);

        switch ($ds) {
            case $dModel::DELIVERY_CDEK:
                $cities = $dModel->getCityByTerm($term, true);
                break;
            case $dModel::DELIVERY_IML:
                $cities = $dModel->getImlCityByTerm($term);
                break;
            case $dModel::DELIVERY_PICK_POINT:
                $cities = $dModel->getPpCityByTerm($term);
                break;
            default:
                return [];
        }

        return [
            'delivery_list' => $delivery_list,
            'cities' => $cities,
        ];
    }

    /**
     * Возвращает сумму и срок доставки для города (Для СДЭК с разбивкой на список тарифов)
     * @param $city
     * @param int $cdek_city_code Код города СДЭК
     * @return array
     */
    public function actionGetDeliveryInfo($city, $cdek_city_code = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $dModel = new Delivery();
        return $dModel->getDeliveriesInfo($city, $cdek_city_code);
    }

    public function actionGetSdekCourierSumAndPeriod($cdek_city_code)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $dModel = new Delivery();

        return $dModel->getSdekSumAndPeriod($cdek_city_code, Delivery::CDEK_SD_DELIVERY_TYPE);
    }

    public function actionGetSdekPvzSumAndPeriod($cdek_city_code)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $dModel = new Delivery();

        return $dModel->getSdekSumAndPeriod($cdek_city_code, Delivery::CDEK_SS_DELIVERY_TYPE);
    }

    public function actionGetImlCourierSumAndPeriod($city_name)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $dModel = new Delivery();

        return $dModel->getImlSumAndPeriod($city_name, Delivery::IML_SD_DELIVERY_TYPE);
    }

    public function actionGetImlPvzSumAndPeriod($city_name)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $dModel = new Delivery();

        return $dModel->getImlSumAndPeriod($city_name, Delivery::IML_SS_DELIVERY_TYPE);
    }

    public function actionGetPickPointSumAndPeriod($city_name)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $dModel = new Delivery();

        return $dModel->getPickPointSumAndPeriod($city_name, Delivery::IML_SS_DELIVERY_TYPE);
    }

    public function actionGetSdekPvzInfo($cdek_city_code)
    {
        $result['pvz'] = array();

        Yii::$app->response->format = Response::FORMAT_JSON;
        $dModel = new Delivery();

        if ($cdek_city_code) {
            $cdek_pvz = $dModel->getCdekPvz($cdek_city_code);
            if ($cdek_pvz['status']) {
                $result['pvz'] = $cdek_pvz['data'];
            }
        }

        return $result;
    }

    public function actionGetImlPvzInfo($city_name)
    {
        $result['pvz'] = array();

        Yii::$app->response->format = Response::FORMAT_JSON;
        $dModel = new Delivery();

        if (!empty($city_name)) {
            $iml_pvz = $dModel->getImlPvz($city_name);
            if ($iml_pvz) {
                $result['pvz'] = $iml_pvz;
            }
        }

        $dMdl = new Delivery();
        $tariffs = $dMdl->getImlTariffList('reduced_list');
        foreach ($tariffs as $tariff_code => $tariff_description) {
            $tariff = $dMdl->getImlSum($city_name, $tariff_code);
            //Ответ сервера IML
            $total_result['response'][$dMdl::DELIVERY_IML][$tariff_code] = $tariff['response'];

            $iml = &$total_result[$dMdl::DELIVERY_IML];
            if ($tariff['status']) {
                $tariff = $tariff['data'];
                $max_period = ceil((strtotime($tariff['DeliveryDate']) - time()) / (60 * 60 * 24)) + 1;
                $iml[$tariff_code] = [
                    'status' => true,
                    'tariff_id' => $tariff['Request']['Job'],
                    'tariff_name' => $tariff_description,
                    'delivery_date' => date('d.m.Y', strtotime($tariff['DeliveryDate'])),
                    'delivery_period_min' => null,
                    'delivery_period_max' => $max_period,
                    'delivery_price' => ceil($tariff['Price']),
                ];
                if ($tariff['PriceList']) {
                    foreach ($tariff['PriceList'] as $price) {
                        $price['DeliveryDate'] = date('d.m.Y', strtotime($price['DeliveryDate']));
                        $price['Price'] = ceil($price['Price']);
                        $iml[$tariff_code]['price_list'][] = $price;
                    }
                } else {
                    $tariff['PriceList'] = [];
                }
            } else {
                $iml[$tariff_code] = [];
            }
        }

        $result['tariff_list'] = $iml;

        return $result;
    }

    public function actionGetPickPointPvzInfo($city_name)
    {
        $result['pvz'] = array();

        Yii::$app->response->format = Response::FORMAT_JSON;
        $dModel = new Delivery();

        if (!empty($city_name)) {
            $pp_city_code = $dModel->getPpCityCodeByCityName($city_name);
            if ($pp_city_code['status'] && $pp_city_code['data']) {
                $pp_pvz = $dModel->getPickPointPvz($pp_city_code['data']);
                if ($pp_pvz['status']) {
                    $result['pvz'] = $pp_pvz['data'];
                }
            }
        }

        return $result;
    }

    /**
     * @param string $ds Служба доставки
     * @param string $city_name
     * @return array
     * [
     *  'cdek' => [
     *      [
     *          'status' => <true/false ПВЗ активен или нет>
     *          'code' => <(string) Код ПВЗ>
     *          'name' => <(string) Наименование ПВЗ>
     *          'address' => <(string)Улица, дом>,
     *          'city_name' => <(string) Название н.п.>,
     *          'region' => <(string) Регион>,
     *          'x' => <(string) Широта>,
     *          'y' => <(string) Долгота>,
     *          'work_time' => <(string) Часы работы>,
     *          'phone' => <(string) Телефон>,
     *      ]
     *  ]
     * ]
     */
    public function actionPvz($ds, $city_name)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!$city_name) {
            return [
                'status' => false,
                'msg' => 'No city name',
            ];
        }

        $dModel = new Delivery();
        $result = [];
        $display_cost = '';
        $display_date_delivery = '';

        switch ($ds) {
            case $dModel::DELIVERY_CDEK:
                $display_cost = 'none';
                $display_date_delivery = 'none';
                $info = $dModel->getCdekPvzByCityName($city_name);
                if ($info['status']) {
                    foreach ($info['data'] as $pvz) {
                        $result[] = [
                            'status' => $pvz['@attributes']['Status'] === 'ACTIVE',
                            'code' => $pvz['@attributes']['Code'],
                            'name' => $pvz['@attributes']['Name'],
                            'address' => $pvz['@attributes']['Address'],
                            'city_name' => $pvz['@attributes']['City'],
                            'region' => $pvz['@attributes']['RegionName'],
                            'x' => $pvz['@attributes']['coordX'],
                            'y' => $pvz['@attributes']['coordY'],
                            'work_time' => $pvz['@attributes']['WorkTime'],
                            'phone' => $pvz['@attributes']['Phone'],
                        ];

                    }
                } else {
                    $result = $info;
                }
                break;
            case $dModel::DELIVERY_IML:
                $display_cost = 'block';
                $display_date_delivery = 'block';
                $info = $dModel->getImlPvzByCityName($city_name);
                if ($info && count($info)) {
                    foreach ($info as $pvz) {
                        if (!$pvz['FormCity']) {
                            $pvz['FormCity'] = $pvz['FormRegion'];
                        }
                        $result[] = [
                            'status' => true,
                            'code' => $pvz['Code'],
                            'name' => $pvz['Name'],
                            'address' => $pvz['Address'],
                            'city_name' => $pvz['FormCity'],
                            'region' => $pvz['FormRegion'],
                            'x' => $pvz['Longitude'],
                            'y' => $pvz['Latitude'],
                            'work_time' => $pvz['WorkMode'],
                            'phone' => $pvz['Phone'],
                        ];
                    }
                }
                break;
            case $dModel::DELIVERY_PICK_POINT:
                $display_cost = 'none';
                $display_date_delivery = 'none';
                $info = $dModel->getPickPointPvzByCityName($city_name);
                if ($info['status']) {
                    foreach ($info['data'] as $pvz) {
                        $result[] = [
                            'status' => $pvz['status'] == 2,
                            'code' => $pvz['terminal_id'],
                            'name' => $pvz['name'],
                            'address' => $pvz['address'],
                            'city_name' => $pvz['city_name'],
                            'region' => $pvz['region'],
                            'x' => $pvz['longitude'],
                            'y' => $pvz['latitude'],
                            'work_time' => $pvz['work_time_sms'],
                            'phone' => null,
                        ];
                    }
                }


                break;
        }
        Yii::warning($result, 'test');

        $list = '<ul>';
        $counter = 0;

        foreach ($result as $pvz) {
            $work_time = $pvz['work_time'] ? 'Режим работы: ' . $pvz['work_time'] : '';
            $phone = $pvz['phone'] ? 'Тел: ' . $pvz['phone'] : '';

            if ($counter % 2) {
                $class = 'odd';
            } else {
                $class = 'even';
            }
            $list .= <<<HTML
            <li class="address-pvz-item {$class}">
                <a href="#">
                    <div class="item-info" data-pvz-code="{$pvz['code']}">
                        <p class="item-info-address-pvz">{$pvz['city_name']}, {$pvz['address']} </p>
                        <p class="item-info-delivery-price" style="display: {$display_cost}">Стоимость: <span></span></p>
                        <p class="item-info-delivery-date" style="display: {$display_date_delivery}">Дата поступления: <span></span></p>
                        <p><span class="addresses-pvz-work-time">{$work_time}</p>
                        <p><span class="addresses-pvz-phone">{$phone}</p>
                    </div>
                </a>
            </li>
HTML;
            $counter++;
        }
        $list .= '</ul>';
        return [
            'html' => $list,
            'data' => $result,
        ];

    }

    public function actionGetCommissionInfo($payment_type_id)
    {
        $result['info'] = '';
        $result['percent'] = 0;
        $commission = null;

        Yii::$app->response->format = Response::FORMAT_JSON;

        $sale_ms_id = Yii::$app->client->identity->profile->sale_ms_id;
        if ($sale_ms_id) {
            $commission = Commission::find()
                ->where(['payment_type_id' => $payment_type_id])
                ->andWhere(['like', 'discount_group', $sale_ms_id])
                ->one();
        }

        if ($commission) {
            $result['info'] = $commission->text;
            $result['percent'] = $commission->percent;
        }

        return $result;
    }

    public function actionCouponApply($code)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $cart = Yii::$app->client->identity->getCart();
        if (!$cart) {
            return [
                'status' => false,
                'msg' => 'Корзина с товарами не найдена',
            ];
        }

        $cart->removeGifts();
        if (!$code) {
            $cart->sum_discount = null;
            $cart->coupon_id = null;
            $cart->save(false);

            return [
                'status' => true,
                'msg' => '',
            ];
        }

        $coupon = Coupons::find()->where(['coupon_code' => $code])->one();
        if (!$coupon) {
            return [
                'status' => false,
                'msg' => 'Неверный промокод',
            ];
        }

        if (!empty($coupon->getClientGroupsArray())) {
            if (!in_array(Yii::$app->client->identity->userProfile->saleTemplateId, $coupon->getClientGroupsArray())) {
                return [
                    'status' => false,
                    'msg' => "Данный купон не предназначен для вашей группы клиентов",
                ];
            }
        }

        return $coupon->resultApply($cart);
    }
}
