<?php

namespace common\models;

use common\models\traits\BlameAble;
use Exception;
use trntv\filekit\behaviors\UploadBehavior;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\httpclient\Client;

class UserClientProfile extends ActiveRecord
{
    use BlameAble;

    const GENDER_MALE = 1;
    const GENDER_FEMALE = 2;

    /**
     * @var
     */
    public $picture;
    public $locale = null;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_client_profile}}';
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return array_merge(
            $this->ts_behavior(),
            [
                'picture' => [
                    'class' => UploadBehavior::class,
                    'attribute' => 'picture',
                    'pathAttribute' => 'avatar_path',
                    'baseUrlAttribute' => 'avatar_base_url'
                ]
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id','full_name','phone', 'mail'], 'required'],
            [['mail'], 'string'],
            [['client_ms_id', 'sale_ms_id'], 'string'],
            [['mail'], 'validateMail'],
            [['user_id'], 'integer'],
            [['sale_change'], 'integer'],
            [['full_name', 'avatar_path', 'phone', 'phone_1', 'address_delivery', 'address_comment', 'ms_owner', 'ms_bonus', 'ms_owner_vk', 'ms_owner_whatsapp', 'ms_owner_name_at_site'], 'string', 'max' => 255],
            [['link_vk', 'link_inst'], 'string', 'max' => 255],
            [['picture'], 'safe'],
            [['sale_brands'], 'string'],
            [['hide_cash', 'hide_card'], 'integer'],
            [['hide_cash', 'hide_card'], 'default', 'value' => 0],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => t_b('Ид. польз.'),
            'full_name' => t_b('ФИО'),
            'pictures' => t_b('Аватар'),
            'phone' => t_b('Телефон'),
            'phone_1' => t_b('Телефон 2'),
            'address_delivery' => t_b('Адрес доставки'),
            'address_comment' => t_b('Комментарий к адресу'),
            'link_vk' => t_b('В контакте'),
            'link_inst' => t_b('Инстаграм'),
            'sale_ms_id' => t_b('Ид. скидки'),
            'created_at' => t_b( 'Создан (админ)'),
            'updated_at' => t_b( 'Обновлен (админ)'),
            'created_by' => t_b('Создал (админ)'),
            'updated_by' => t_b('Обновил (админ)'),
            'client_created_at' => t_b( 'Создан (клиент)'),
            'client_updated_at' => t_b( 'Обновлен (клиент)'),
            'client_created_by' => t_b('Создал (клиент)'),
            'client_updated_by' => t_b('Обновил (клиент)'),
            'client_ms_id' => 'Ид. в МС контрагента',
            'sale_brands' => 'Скидки по брендам, через запятую',
            'ms_owner' => t_b('Ваш менеджер'),
            'ms_bonus' => t_b('Бонусный счёт'),
            'ms_owner_vk' => t_b('Страница менеджера Вконтакте'),
            'ms_owner_whatsapp' => t_b('WhatsApp менеджера'),
            'ms_owner_name_at_site' => t_b('Имя на сайте менеджера'),
            'hide_cash' => t_b('Запретить оплату при получении'),
            'hide_card' => t_b('Запретить оплату банковской картой'),
        ];
    }

    public function afterSave($insert, $changedAttributes){
        parent::afterSave($insert, $changedAttributes);

        $this->ms_update_client();
    }

    public function getSalesBrandsArr() {
        $result = [];
        $preResult = [];

        try {
            $preResult = !empty($this->sale_brands) ?
                json_decode($this->sale_brands) : [];
        } catch (Exception $e) {}

        foreach ($preResult as $sale => $brands) {
            $brands = str_replace(' , ', ',', $brands);
            $brands = str_replace(', ', ',', $brands);
            $brands = str_replace(' ,', ',', $brands);
            $result[$sale] = explode(',', $brands);
        }

        return $result;
    }

    public function getSalesBrandsSales() {
        $result = [];

        foreach($this->salesBrandsArr as $sale => $brands) {
            for ($i = 0; $i < count($brands); $i++)
                $result[] = $sale;
        }

        return $result;
    }

    public function getSalesBrandsBrands() {
        $result = [];

        foreach($this->salesBrandsArr as $sale => $brands) {
            for ($i = 0; $i < count($brands); $i++)
                $result[] = $brands[$i];
        }

        return $result;
    }

    public function getSalesBrandsTmpIds()
    {
        $db = Yii::$app->db;
        $t = $db->cache(function ($db) {
            return ProductPriceTemplate::find()
                ->where(['in', 'title', $this->getSalesBrandsSales()])
                ->asArray()
                ->all();
        }, 86400);

        $tmp = ArrayHelper::map($t, 'title', 'id');
        $result = [];

        foreach ($this->salesBrandsArr as $sale => $brands) {
            for ($i = 0; $i < count($brands); $i++)
                $result[] = $tmp[$sale];
        }

        return $result;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(UserClient::class, ['id' => 'user_id']);
    }

    /**
     * @return null|string
     */
    public function getFullName()
    {
        return $this->full_name;
    }

    /**
     * @param null $default
     * @return bool|null|string
     */
    public function getAvatar($default = null)
    {
        return $this->avatar_path
            ? Yii::getAlias($this->avatar_base_url . '/' . $this->avatar_path)
            : $default;
    }

    public function getMail() {
        return $this->user->email;
    }

    public function setMail( $v ) {
        $user = $this->user;
        $user->email = $v;
        $user->save();
    }

    public function validateMail($attribute)
    {
        if (UserClient::find()
            ->andWhere(['email' => $this->$attribute])
            ->andWhere(['<>', 'id', $this->user->id])
            ->one()) {
            $this->addError($attribute, 'Email занят.');
        }
    }

    public function getSaleTemplateId()
    {
        $db = Yii::$app->db;
        $t = $db->cache(function ($db) {
            return ProductPriceTemplate::find()
                ->where(['title' => $this->sale_ms_id])
                ->one();
        }, 86400);

        return $t ? $t->id : ProductPriceTemplate::TEMPLATE_ID_DEFAULT;
    }

    public function getSearchTerm() {
        $phone = $this->phone;
        if ($phone) {
            if (substr($phone, 0, 3) == '007') {
                $phone = substr_replace($phone,"8",0, 3);
            } elseif (substr($phone, 0, 2) == '+7' ) {
                $phone = substr_replace($phone,"8",0, 2);
            } elseif (substr($phone, 0, 1) == '7') {
                $phone = substr_replace($phone,"8",0, 1);
            }
            $phone = preg_replace('/[^+0-9]/', '', $phone);
        }

        return $phone;
    }

    public function sync() {
        if (!empty($this->client_ms_id)) {
            $search = $this->client_ms_id;
            $type = 'id';
        } else {
            $search = $this->searchTerm;
            $type = 'search';
        }

        $sync = '';
        if (!empty($search)) {
            $sync = $this->syncClient($search, $type); // поиск по ms_id или номеру телефона
        }

        if (!empty($sync)) {
            $changeSale = $this->sale_ms_id != $sync->sale_ms_id || $this->sale_brands != $sync->sale_brands;
            $this->sale_ms_id = $sync->sale_ms_id;
            $this->client_ms_id = $sync->client_ms_id;
            $this->sale_brands = $sync->sale_brands;
            $this->sale_change = (int) $changeSale;
            $this->save();
        } else {
            $client = $this->ms_create_client();
            if ($client->status) {
                $this->client_ms_id = isset($client->responseContent->id) ? $client->responseContent->id : null;
                $this->sale_ms_id = isset($client->responseContent->priceType) ? $client->responseContent->priceType->name : null;
                $this->save(false);
            }
        }
    }

    protected function syncClient($search, $type = 'search') {

        if (empty($search))
            return null;

        $client = new Client([
            'baseUrl' => 'https://api.moysklad.ru/api/remap/1.2/entity/'
        ]);

        $request = $client->createRequest()
            ->setMethod('GET')
            ->setHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => "Basic ".base64_encode(env('MS_LOGIN').':'.env('MS_PASSWORD')),
                'Accept-Encoding' => 'gzip, deflate, br',
            ])
            ->setData(['limit' => 1])
            ->addOptions([
                'timeout' => 15
            ]);

        if ($type == 'search')
            $request->setUrl("counterparty?search=$search");
        else
            $request->setUrl("counterparty?filter=$type=$search");

        $response = $request->send();

        if (!$response->isOk)
            return false;

        try {
            if ($response->headers['content-encoding'] == 'gzip') {
                $responseContent = json_decode(gzdecode($response->content));
            } else {
                $responseContent = json_decode($response->content);
            }

            if (is_null($responseContent) || json_last_error())
                return false;

            $responseContent = $responseContent->rows;

            if (empty($responseContent) || !isset($responseContent[0]))
                return null;

            return (object) [
                'client_ms_id' => $responseContent[0]->id,
                'sale_ms_id' => isset($responseContent[0]->priceType) ? $responseContent[0]->priceType->name : null,
                'sale_brands' => isset($responseContent[0]->attributes) ?
                    $this->prepareSalesBrands($responseContent[0]->attributes) : null
            ];
        } catch (Exception $e) {
            \Yii::error([$e, 'sync client: '.$this->id], 'client_sync__except');
            return false;
        }
    }

    public static function searchClient($search_ms_id) {
        if (empty($search_ms_id))
            return null;

        $client = new Client([
            'baseUrl' => 'https://api.moysklad.ru/api/remap/1.2/entity/'
        ]);

        $request = $client->createRequest()
            ->setMethod('GET')
            ->setHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => "Basic ".base64_encode(env('MS_LOGIN').':'.env('MS_PASSWORD')),
                'Accept-Encoding' => 'gzip, deflate, br',
            ])
            ->setData(['limit' => 1])
            ->addOptions([
                'timeout' => 15
            ]);

        $request->setUrl("counterparty?filter=id=$search_ms_id");
        $response = $request->send();

        if (!$response->isOk)
            return false;

        try {
            if ($response->headers['content-encoding'] == 'gzip') {
                $responseContent = json_decode(gzdecode($response->content));
            } else {
                $responseContent = json_decode($response->content);
            }
            if (is_null($responseContent) || json_last_error())
                return false;

            $responseContent = $responseContent->rows;

            if (empty($responseContent) || !isset($responseContent[0])) {
                return null;
            }

            $attr = array(
                'link_vk' => null,
                'link_inst' => null,
                'bonus' => null,
            );
            if (isset($responseContent[0]->attributes) && is_array($responseContent[0]->attributes)) {
                foreach ($responseContent[0]->attributes as $item) {
                    switch ($item->id) {
                        case env('MS_ID_CUSTOM_COUNTERPARTY_VCONTACT'):
                            $attr['link_vk'] = $item->value;
                            break;
                        case env('MS_ID_CUSTOM_COUNTERPARTY_INSTA'):
                            $attr['link_inst'] = $item->value;
                            break;
                        case '0903dcf9-27da-11eb-0a80-064800224ef1':
                            $attr['bonus'] = $item->value;
                            break;
                    }
                }
            }

            $owner = null;
            if (isset($responseContent[0]->owner->meta->href)) {
                $ms_owner = self::ms_send_req($responseContent[0]->owner->meta->href, null, $method = 'GET');
                $owner = isset($ms_owner->responseContent->name) ? $ms_owner->responseContent->name : null;
            }

            $ms_order_count = null;
            if (isset($responseContent[0]->meta->href)) {
                $resp = self::ms_send_req("https://api.moysklad.ru/api/remap/1.2/entity/customerorder?filter=agent={$responseContent[0]->meta->href}", null, $method = 'GET');

                if (isset($resp->responseContent->rows) && is_array($resp->responseContent->rows)) {
                    $ms_order_count = count($resp->responseContent->rows);
                }
            }

            return (object) [
                'client_ms_id' => $responseContent[0]->id,
                'full_name' => $responseContent[0]->name,
                'address_delivery' => $responseContent[0]->actualAddress,
                //'address_comment' => isset($responseContent[0]->actualAddressFull->comment) ? $responseContent[0]->actualAddressFull->comment : null,
                'email' => $responseContent[0]->email,
                'phone' => $responseContent[0]->phone,
                'phone_1' => $responseContent[0]->fax,
                'link_vk' => $attr['link_vk'],
                'link_inst' => $attr['link_inst'],
                'owner' => $owner,
                'bonus' => $attr['bonus'],
                'ms_order_count' => $ms_order_count,
                'sale_ms_id' => isset($responseContent[0]->priceType) ? $responseContent[0]->priceType->name : null,
                'sales_amount' => isset($responseContent[0]->salesAmount) ? $responseContent[0]->salesAmount : null,
                'client' => $responseContent[0],
                //'sale_brands' => isset($responseContent[0]->attributes) ? $this->prepareSalesBrands($responseContent[0]->attributes) : null
            ];
        } catch (Exception $e) {
            \Yii::error([$e, 'sync client: '.$search_ms_id], 'client_sync__except');
            return false;
        }
    }

    public static function searchClientByPhone($phone)
    {
        if (substr($phone, 0, 3) == '007') {
            $phone = substr_replace($phone, "8", 0, 3);
        } elseif (substr($phone, 0, 2) == '+7') {
            $phone = substr_replace($phone, "8", 0, 2);
        } elseif (substr($phone, 0, 1) == '7') {
            $phone = substr_replace($phone, "8", 0, 1);
        }
        $phone = preg_replace('/[^+0-9]/', '', $phone);

        $client = new Client([
            'baseUrl' => 'https://api.moysklad.ru/api/remap/1.2/entity/'
        ]);

        $request = $client->createRequest()
            ->setMethod('GET')
            ->setUrl("counterparty?search=$phone")
            ->setHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => "Basic " . base64_encode(env('MS_LOGIN') . ':' . env('MS_PASSWORD')),
                'Accept-Encoding' => 'gzip, deflate, br',
            ])
            ->setData(['limit' => 1])
            ->addOptions([
                'timeout' => 15,
            ]);

        $response = $request->send();

        if ($response->isOk) {
            try {
                $responseContent = $response->content;
                if ($response->headers['content-encoding'] == 'gzip') {
                    $responseContent = gzdecode($response->content);
                }

                $responseContent = json_decode($responseContent);
                if (is_null($responseContent) || json_last_error())
                    return false;

                $responseContent = $responseContent->rows;

                if (empty($responseContent) || !isset($responseContent[0]))
                    return null;

                return [
                    'client_ms_id' => $responseContent[0]->id,
                ];
            } catch (Exception $e) {
                //\Yii::error([$e, 'sync client: '.$this->id], 'client_sync__except');
            }
        }

        return false;
    }

    /**
     * @param $url
     * @param null $data
     * @param string $method
     * @return object
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    protected static function ms_send_req($url, $data = null, $method = 'GET')
    {
        $result = (object)[
            'status' => true,
            'response' => null,
            'responseContent' => null,
            'json_error' => false,
            'msg' => null
        ];

        $client = new Client([
            'baseUrl' => 'https://api.moysklad.ru/api/remap/1.2/',
            'requestConfig' => [
                'format' => Client::FORMAT_JSON
            ],
        ]);

        $request = $client->createRequest()
            ->setMethod($method)
            ->setUrl($url)
            ->setHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => "Basic " . base64_encode(env('MS_LOGIN') . ':' . env('MS_PASSWORD')),
                'Accept-Encoding' => 'gzip, deflate, br',
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
                if ($response->headers['content-encoding'] == 'gzip') {
                    $responseContent = json_decode(gzdecode($response->content));
                } else {
                    $responseContent = json_decode($response->content);
                }
                $result->responseContent = $responseContent;
                if (is_null($responseContent) || $e = json_last_error()) {
                    $result->status = false;
                    $result->msg = 'JSON parse error (' . $e . ')';
                    $result->json_error = true;
                    return $result;
                }
            }

            return $result;

        } catch (Exception $e) {
            $result->status = false;
            $result->msg = (string)$e;
            return $result;
        }
    }

    protected function prepareSalesBrands($attrs) {
        $result = [];

        foreach($attrs as $attr) {
            if (mb_strpos($attr->name, 'Скидка') === 0)
                $result[$attr->name] = $attr->value;
        }

        return count($result) ? json_encode($result) : null;
    }

    protected function ms_update_client() {
        $phone = $this->phone;
        if ($phone) {
            if (substr($phone, 0, 3) == '007') {
                $phone = substr_replace($phone,"8",0, 3);
            } elseif (substr($phone, 0, 2) == '+7' ) {
                $phone = substr_replace($phone,"8",0, 2);
            } elseif (substr($phone, 0, 1) == '7') {
                $phone = substr_replace($phone,"8",0, 1);
            }
            //$phone = str_replace('+7', '8', $phone);
            $phone = preg_replace('/[^+0-9]/', '', $phone);
        }

        $fax = $this->phone_1;
        if ($fax) {
            if (substr($fax, 0, 3) == '007') {
                $fax = substr_replace($fax,"8",0, 3);
            } elseif (substr($fax, 0, 2) == '+7' ) {
                $fax = substr_replace($fax,"8",0, 2);
            } elseif (substr($fax, 0, 1) == '7') {
                $fax = substr_replace($fax,"8",0, 1);
            }
            $fax = preg_replace('/[^+0-9]/', '', $fax);
        }

        return $this->ms_send_sync(
            "entity/counterparty/{$this->client_ms_id}",
            [
                "actualAddress" =>  $this->address_delivery,
                "code" => $this->user->username,
                "email" => $this->user->email,
                "phone" => $phone,
                "fax" => $fax,
                "attributes" => [
                    [
                        "id" => env('MS_ID_CUSTOM_COUNTERPARTY_FIO'),
                        "meta" => [
                            "href" => "https://api.moysklad.ru/api/remap/1.2/entity/counterparty/metadata/attributes/".env('MS_ID_CUSTOM_COUNTERPARTY_FIO'),
                            "type" => "attributemetadata",
                            "mediaType" => "application/json"
                        ],
                        "name" => "ФИО на сайте",
                        "type" => "string",
                        "value" => $this->full_name
                    ],
                    [
                        "id" => env('MS_ID_CUSTOM_COUNTERPARTY_VCONTACT'),
                        "meta" => [
                            "href" => "https://api.moysklad.ru/api/remap/1.2/entity/counterparty/metadata/attributes/".env('MS_ID_CUSTOM_COUNTERPARTY_VCONTACT'),
                            "type" => "attributemetadata",
                            "mediaType" => "application/json"
                        ],
                        "name" => "Vkontakte",
                        "type" => "link",
                        "value" => $this->link_vk
                    ],
                    [
                        "id" => env('MS_ID_CUSTOM_COUNTERPARTY_INSTA'),
                        "meta" => [
                            "href" => "https://api.moysklad.ru/api/remap/1.2/entity/counterparty/metadata/attributes/".env('MS_ID_CUSTOM_COUNTERPARTY_INSTA'),
                            "type" => "attributemetadata",
                            "mediaType" => "application/json"
                        ],
                        "name" => "Instagram",
                        "type" => "link",
                        "value" => $this->link_inst
                    ],
                ]
            ],
            $method = 'PUT'
        );

    }

    protected function ms_send_sync($url, $data = null, $method = 'GET') {

        $result = (object) [
            'status' => true,
            'response' => null,
            'responseContent' => null,
            'json_error' => false,
            'msg' => null
        ];

        $client = new Client([
            'baseUrl' => 'https://api.moysklad.ru/api/remap/1.2/',
            'requestConfig' => [
                'format' => Client::FORMAT_JSON
            ],
        ]);

        $request = $client->createRequest()
            ->setMethod($method)
            ->setUrl($url)
            ->setHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => "Basic ".base64_encode(env('MS_LOGIN').':'.env('MS_PASSWORD')),
                'Accept-Encoding' => 'gzip, deflate, br',
            ])
            ->addOptions([
                'timeout' => 15
            ]);

        if ($data)
            $request->setData($data);

        $response = $request->send();
        $result->response = $response;

        if (!$response->isOk) {
            $result->status = false;
            $result->msg = $response->content;
            return $result;
        }

        try {
            if (isset($response->content)) {
                if ($response->headers['content-encoding'] == 'gzip') {
                    $responseContent = json_decode(gzdecode($response->content));
                } else {
                    $responseContent = json_decode($response->content);
                }
                $result->responseContent = $responseContent;
                if (is_null($responseContent) || $e = json_last_error()) {
                    $result->status = false;
                    $result->msg = 'JSON parse error ('. $e .')';
                    $result->json_error = true;
                    return $result;
                }
            }

            return $result;

        } catch (Exception $e) {
            $result->status = false;
            $result->msg = (string) $e;
            return $result;
        }
    }

    /**
     * @return object
     */
    protected function ms_create_client(): object
    {
        $phone = $this->phone;
        if ($phone) {
            if (substr($phone, 0, 3) == '007') {
                $phone = substr_replace($phone,"8",0, 3);
            } elseif (substr($phone, 0, 2) == '+7' ) {
                $phone = substr_replace($phone,"8",0, 2);
            } elseif (substr($phone, 0, 1) == '7') {
                $phone = substr_replace($phone,"8",0, 1);
            }
            $phone = preg_replace('/[^+0-9]/', '', $phone);
        }

        $fax = $this->phone_1;
        if ($fax) {
            if (substr($fax, 0, 3) == '007') {
                $fax = substr_replace($fax,"8",0, 3);
            } elseif (substr($fax, 0, 2) == '+7' ) {
                $fax = substr_replace($fax,"8",0, 2);
            } elseif (substr($fax, 0, 1) == '7') {
                $fax = substr_replace($fax,"8",0, 1);
            }
            $fax = preg_replace('/[^+0-9]/', '', $fax);
        }

        return $this->ms_send_sync(
            'entity/counterparty',
            [
                "name" => $this->full_name ?: '',
                "description" => "Регистрация через сайт",
                "actualAddress" => $this->address_delivery ?: '',
                "email" => $this->user->email ?: '',
                "phone" => $phone ?: '',
                "fax" => $fax ?: '',
                "attributes" => [
                    [
                        "id" => env('MS_ID_CUSTOM_COUNTERPARTY_FIO'),
                        "meta" => [
                            "href" => "https://api.moysklad.ru/api/remap/1.2/entity/counterparty/metadata/attributes/".env('MS_ID_CUSTOM_COUNTERPARTY_FIO'),
                            "type" => "attributemetadata",
                            "mediaType" => "application/json"
                        ],
                        "name" => "ФИО на сайте",
                        "type" => "string",
                        "value" => $this->full_name
                    ],
                    [
                        "id" => env('MS_ID_CUSTOM_COUNTERPARTY_VCONTACT'),
                        "meta" => [
                            "href" => "https://api.moysklad.ru/api/remap/1.2/entity/counterparty/metadata/attributes/".env('MS_ID_CUSTOM_COUNTERPARTY_VCONTACT'),
                            "type" => "attributemetadata",
                            "mediaType" => "application/json"
                        ],
                        "name" => "Vkontakte",
                        "type" => "link",
                        "value" => $this->link_vk
                    ],
                    [
                        "id" => env('MS_ID_CUSTOM_COUNTERPARTY_INSTA'),
                        "meta" => [
                            "href" => "https://api.moysklad.ru/api/remap/1.2/entity/counterparty/metadata/attributes/".env('MS_ID_CUSTOM_COUNTERPARTY_INSTA'),
                            "type" => "attributemetadata",
                            "mediaType" => "application/json"
                        ],
                        "name" => "Instagram",
                        "type" => "link",
                        "value" => $this->link_inst
                    ],
                ]
            ],
            'POST'
        );
    }
}
