<?php

namespace frontend\controllers;

use cheatsheet\Time;
use chumakovanton\tinkoffPay\request\RequestInit;
use common\components\Common;
use common\loggers\CustomLogger;
use common\models\BlockWidget;
use common\models\Commission;
use common\models\Page;
use common\models\PaymentTypes;
use frontend\models\BuyOneClickForm;
use frontend\models\BuyOneClickCartForm;
use frontend\models\PayForm;
use common\models\UserClientOrder;
use frontend\models\Product;
use common\models\Brand;
use common\models\ProductCategory;
use common\models\SliderMain;
use frontend\models\ContactForm;
use frontend\models\ReviewsForm;
use frontend\models\NotFoundSearchForm;
use frontend\models\Roistat;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\PageCache;
use yii\httpclient\Client;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\helpers\Url;
use yii\base\ErrorException;
use yii\web\XmlResponseFormatter;

/**
 * Site controller
 */
class SiteController extends BaseController
{
    public static $crm_user_client_ids = [ // аккаунты менеджеров CRM, через которые оформлляются заказы из CRM
        1537 => 'f1e6c7dd-9e21-11ed-0a80-11510004e2db', // p.ermakov@tattoo-manager.com
        1539 => 'ca1e77e4-9e22-11ed-0a80-0ed80004fa3e', // smirnov@tattoo-manager.com
    ];

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            [
                'class' => PageCache::class,
                'only' => ['sitemap'],
                'duration' => Time::SECONDS_IN_AN_HOUR,
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction'
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null
            ],
            'set-locale' => [
                'class' => 'common\actions\SetLocaleAction',
                'locales' => array_keys(Yii::$app->params['availableLocales'])
            ]
        ];
    }

    /**
     * @return array
     * @throws NotFoundHttpException
     * @var $term string
     */
    public function actionSearch($term = '')
    {

        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!Yii::$app->request->isAjax || Yii::$app->request->isPjax)
            throw new NotFoundHttpException();

        $preResult = Product::find()
            ->preparePrice()
            ->published()
            ->search($term)
            ->limit(10)
            ->all();

        $result = [];

        foreach ($preResult as $model)
            $result[] = [
                'label' => $model->title,
                'id' => $model->id,
                'imgUrl' => $model->getImgUrl(),
                'url' => Url::to($model->getRoute()),
                'price' => $model->getFrontendCurrentPrice(),
                'q' => $term,
            ];

        return $result;
    }

    /**
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index', [
            'categoryDataProvider' => new ActiveDataProvider([
                'query' => ProductCategory::find()->published()->order()->limit(12),
                'pagination' => false,
            ]),
            'productsNew' => new ActiveDataProvider([
                'query' => Product::find()->new()->limit(16),
                'pagination' => false,
            ]),
            'productsSale' => new ActiveDataProvider([
                'query' => Product::find()->sale()->limit(16),
                'pagination' => false,
            ]),
            'productsPopular' => new ActiveDataProvider([
                'query' => Product::find()->popular()->limit(16),
                'pagination' => false,
            ]),
            'brandDataProvider' => new ActiveDataProvider([
                'query' => Brand::find()->published()->limit(10),
                'pagination' => false,
            ]),
            'mainBannerDataProvider' => new ActiveDataProvider([
                'query' => SliderMain::find()->orderBy(['created_at'=> SORT_DESC])->published(),
                'pagination' => false,
            ]),
            'teamWidget' => BlockWidget::find()->where(['widget_id' => 'home-page__team-ban'])->published()->one()
        ]);
    }

    /**
     * @return string
     */
    public function actionPayCard()
    {
        return $this->render('pay-card');
    }

    /**
     * @param string $format
     * @param bool $gzip
     * @return string
     * @throws BadRequestHttpException
     * public function actionSitemap($format = Sitemap::FORMAT_XML, $gzip = false)
     * {
     * $links = new UrlsIterator();
     * $sitemap = new Sitemap(new Urlset($links));
     *
     * Yii::$app->response->format = Response::FORMAT_RAW;
     *
     * if ($gzip === true) {
     * Yii::$app->response->headers->add('Content-Encoding', 'gzip');
     * }
     *
     * if ($format === Sitemap::FORMAT_XML) {
     * Yii::$app->response->headers->add('Content-Type', 'application/xml');
     * $content = $sitemap->toXmlString($gzip);
     * } else if ($format === Sitemap::FORMAT_TXT) {
     * Yii::$app->response->headers->add('Content-Type', 'text/plain');
     * $content = $sitemap->toTxtString($gzip);
     * } else {
     * throw new BadRequestHttpException('Unknown format');
     * }
     *
     * $linksCount = $sitemap->getCount();
     * if ($linksCount > 50000) {
     * Yii::warning(\sprintf('Sitemap links count is %d'), $linksCount);
     * }
     *
     * return $content;
     * }
     */

    public function actionShowReviewsForm()
    {
        $model = new ReviewsForm();

        return $this->renderPartial('modal/reviews', [
            'model' => $model,
        ]);
    }

    public function actionSendReview()
    {
        $model = new ReviewsForm();
        //$model->load(Yii::$app->request->post());

        if ($model->load(Yii::$app->request->post())) {
            $result = $model->contact(Yii::$app->params['adminEmail']);

            $roistat = new Roistat();
            $roistatFormField = 'Tattoofeel: предложения и отзывы';
            $roistat->handlerForm($model, $roistatFormField);


            if (true === $result) {
                //$success = true;
                return 'success';
            } elseif (false === $result) {
                return 'error';
            }
        }/* else {
            return $this->renderPartial('modal/reviews', [
                'model' => $model,
            ]);
        }*/

        return $this->renderPartial('modal/reviews', [
            'model' => $model,
            //'errors' => $model->errors
        ]);
    }

    public function actionShowBuyOneClickForm()
    {
        $model = new BuyOneClickForm();

        return $this->renderPartial('modal/buy-one-click', [
            'model' => $model,
        ]);
    }

    public function actionSendBuyOneClick()
    {
        $model = new BuyOneClickForm();

        if ($model->load(Yii::$app->request->post())) {
            $result = $model->contact(Yii::$app->params['adminEmail']);

            $roistat = new Roistat();
            $roistatFormField = 'Tattoofeel: покупка в один клик';
            $roistat->handlerForm($model, $roistatFormField);

            if (true === $result) {
                return 'success';
            } elseif (false === $result) {
                return 'error';
            }
        }

        return $this->renderPartial('modal/buy-one-click', [
            'model' => $model,
        ]);
    }

    public function actionShowBuyOneClickCartForm()
    {
        $model = new BuyOneClickCartForm();

        return $this->renderPartial('modal/buy-one-click-cart', [
            'model' => $model,
        ]);
    }

    public function actionSendBuyOneClickCart()
    {
        $model = new BuyOneClickCartForm();

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            //Yii::$app->response->format = Response::FORMAT_JSON;

            $roistat = new Roistat();
            $roistatFormField = 'Tattoofeel: покупка в один клик CartForm';
            $roistat->handlerForm($model, $roistatFormField);

            if ($model->validate()) {
                if ($model->createOrder()) {
                    return 'success';
                } else {
                    return 'error';
                }
            }
        }

        return $this->renderPartial('modal/buy-one-click-cart', [
            'model' => $model,
        ]);
    }

    public function actionShowPayForm()
    {
        $request = Yii::$app->request->get();

        $order = '';
        $commission = null;
        if ($request['order_id']) {
            $order = UserClientOrder::find()->where(['id' => $request['order_id']])->one();

            $sale_ms_id = Yii::$app->client->identity->profile->sale_ms_id;
            if ($order && $sale_ms_id) {
                $commission = Commission::find()
                    ->where(['payment_type_id' => $order->payment_type])
                    ->andWhere(['like', 'discount_group', $sale_ms_id])
                    ->one();
            }
        }

        $model = new PayForm();

        return $this->renderPartial('modal/pay', [
            'model' => $model,
            'order' => $order,
            'paymentTypes' => PaymentTypes::find()->where(['active' => true])->all(),
            'commission' => $commission,
        ]);
    }

    public function actionChangePay()
    {
        $post = Yii::$app->request->post();
        $order = UserClientOrder::find()->where(['id' => $post['order_id']])->one();
        if ($order) {
            $order->payment_type = $post['payment_type'];
            $order->commission = $order->commissionPercent;

            $order->save(false);

            $order->msChangePaymentType();
        }

        return $this->redirect('/lk/orders/');
    }

    public function actionShowNotFoundSearchForm()
    {
        $model = new NotFoundSearchForm();

        return $this->renderPartial('modal/not-found-search', [
            'model' => $model,
        ]);
    }

    public function actionSendNotFoundSearch()
    {
        $model = new NotFoundSearchForm();

        if ($model->load(Yii::$app->request->post())) {
            $result = $model->contact(Yii::$app->params['adminEmail']);
            if (true === $result) {
                return 'success';
            } elseif (false === $result) {
                return 'error';
            }
        }

        return $this->renderPartial('modal/not-found-search', [
            'model' => $model,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionTinkoffNotification()
    {
        $request = 'Empty';
        try {
            $request = (array)json_decode(file_get_contents('php://input'));

            /*
            Array
            (
                [TerminalKey] => 1562925611965DEMO
                [OrderId] => 20
                [Success] => 1
                [Status] => CONFIRMED
                [PaymentId] => 350780716
                [ErrorCode] => 0
                [Amount] => 400000
                [CardId] => 41843883
                [Pan] => 430000******0777
                [ExpDate] => 1122
                [Token] => e4adb8abb454f6daa8f8b0921809f49782277d5cc477aa000ec861a84000bd66
            )
             */

            $request['Password'] = env("TINKOFF_TERMINAL_PASS");
            ksort($request);
            $original_token = $request['Token'];
            unset($request['Token']);

            $request['Success'] = $request['Success'] === true ? 'true' : 'false';

            $values = '';
            foreach ($request as $key => $val) {
                $values .= $val;
            }

            $token = hash('sha256', $values);

            if ($token == $original_token) {
                $order = UserClientOrder::find()->where(['id' => $request['OrderId']])->one();

                if (!$order) {
                    die('BAD ORDER');
                }

                if ($request['Status'] == 'CONFIRMED') {
                    $order->scenario = 'pay';
                    $order->status_pay = UserClientOrder::STATUS_PAY_YES;
                    $order->date_pay = time();
                    $order->pay_id = 'tinkoff pay';
                    $order->save(false);

                    if (in_array($order->user_id, array_keys(self::$crm_user_client_ids))) { // для CRM
                        $params = array(
                            'OrderId' => $order->order_ms_id,
                            'Success' => $request['Success'],
                            'Status' => $request['Status'],
                            'PaymentId' => $request['PaymentId'],
                            'ErrorCode' => $request['ErrorCode'],
                            'Amount' => $request['Amount'],
                            //'CardId' => 41843883,
                            //'Pan' => '430000******0777',
                            //'ExpDate' => '1122',
                        );
                        $result = $this->CRM_send('tattoofeel_payment_notification', $params, 'POST');
                    }

                    $fp = fopen(dirname(__FILE__) . '/logs/tinkoff-order.txt', 'w');
                    fwrite($fp, 'OK:');
                    fwrite($fp, print_r($order, true));
                    fclose($fp);
                }

//                $orders = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "woocommerce_order_items WHERE order_id=" . (int)$request['OrderId']);
//                $order_status = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "posts WHERE ID=" . $orders[0]->order_id);
//                $status = $order_status[0]->post_status;
//                $order = wc_get_order((int)$request['OrderId']);
//
//                if ($request['Status'] == 'AUTHORIZED' && $status == 'wc-pending') {
//                    $order_status = 'wc-on-hold';
//                    $order->update_status($order_status);
//                    die('OK');
//                }
//                switch ($request['Status']) {
//                    case 'AUTHORIZED':
//                        $order_status = 'wc-on-hold';
//                        break; /*Деньги на карте захолдированы. Корзина очищается.*/
//                    case 'CONFIRMED':
//                        $order_status = 'wc-processing';
//                        break; /*Платеж подтвержден.*/
//                    case 'CANCELED':
//                        $order_status = 'wc-cancelled';
//                        break; /*Платеж отменен*/
//                    case 'REJECTED':
//                        $order_status = 'wc-failed';
//                        break; /*Платеж отклонен.*/
//                    case 'REVERSED':
//                        $order_status = 'wc-cancelled';
//                        break; /*Платеж отменен*/
//                    case 'REFUNDED':
//                        $order_status = 'wc-refunded';
//                        break; /*Произведен возврат денег клиенту*/
//                }
//
//                if ($request['Status'] === 'CONFIRMED' && $settings['auto_complete'] === 'yes') {
//                    $order_status = 'wc-completed';
//                }
//
//                $order->update_status($order_status);
//                do_action('woocommerce_order_edit_status', (int)$request['OrderId'], $order_status);
//
//                if (function_exists('wcs_get_subscriptions_for_order')) {
//                    write_Rebillid_Tinkoff($request);
//                }

                die('OK');
            } else {
                die('NOT OK');
            }
        } catch (ErrorException $e) {
            //fputs($flog, "Exception: ". $e->getMessage());
            $fp = fopen(dirname(__FILE__) . '/logs/tinkoff-error.txt', 'w');
            fwrite($fp, "ERROR");
            fwrite($fp, print_r($e->getMessage(), true));
            fclose($fp);
            die('ERROR');
        }
    }

    public function actionSendNotificationToAdmin()
    {
        try {
            $date = date('d.m.Y H:i:s');

            Yii::$app->mailer->compose()
                ->setTo([
                    '440807@mail.ru',
                    'medvedgreez@yandex.ru',
                    'toaster16mb@gmail.com',
                ])
                ->setFrom(env('ROBOT_EMAIL'))
                //->setReplyTo($this->replyEmail)
                ->setSubject("Tattoofeel: были удалены товары из каталога [$date]")
                ->setHtmlBody("<p>Это письмо было отправлено, потому что увеличилось количество товаров в каталоге Tattoofeel, у которых проставлен признак удаления из МС.</p><p>Время этого события: $date</p>")
                ->send();

//            $fp = fopen(dirname(__FILE__).'/mail-send.txt', 'w');
//            fwrite($fp, "OK. Mail is send. {$date}");
//            fclose($fp);

            die('ok');
        } catch (ErrorException $e) {
//            $fp = fopen(dirname(__FILE__).'/mail-fail.txt', 'w');
//            fwrite($fp, "ERROR");
//            fclose($fp);

            die('error');
        }
    }

    public function actionFeed($token = 'y3Ysb1dsj92Sg44gxLf8b5Mbarf9cp')
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        Yii::$app->response->headers->add('Content-Type', 'text/xml');

        if ($token === 'y3Ysb1dsj92Sg44gxLf8b5Mbarf9cp') {
            $xml = Yii::$app->homeUrl . '/y3Ysb1dsj92Sg44gxLf8b5Mbarf9cp.xml';

           return file_get_contents($xml);
        }
    }

    protected function CRM_send($url, $data = null, $method = 'GET')
    {
        $result = (object)[
            'status' => true,
            'response' => null,
            'responseContent' => null,
            'json_error' => false,
            'msg' => null
        ];

        $client = new Client([
            'baseUrl' => 'https://staging.tattoo-manager.com/restapi/v1/',
            'requestConfig' => [
                'format' => Client::FORMAT_JSON
            ],
        ]);

        $request = $client->createRequest()
            ->setMethod($method)
            ->setUrl($url)
            ->setHeaders([
                'Content-Type' => 'application/json',
            ])
            ->addOptions([
                'timeout' => 15
            ]);

        if ($data) {
            $request->setData($data);
        }

        $response = $request->send();
        $result->response = $response;

        if (!$response->isOk) {
            $result->status = false;
            $result->msg = $response->content;

            return $result;
        }

        try {
            if (isset($response->content)) {
                $responseContent = json_decode($response->content, true);
                $result->responseContent = $responseContent;
                if (is_null($responseContent) || json_last_error()) {
                    $result->status = false;
                    $result->msg = 'JSON parse error (' . json_last_error() . ')';
                    $result->json_error = true;

                    return $result;
                }
            }

            return $result;

        } catch (\Exception $e) {
            $result->status = false;
            $result->msg = (string)$e;

            return $result;
        }
    }
}
