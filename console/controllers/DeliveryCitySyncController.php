<?php

namespace console\controllers;

use common\models\DeliveryCity;
use common\models\UserClientProfile;
use yii\console\Controller;
use yii\httpclient\Client;


class DeliveryCitySyncController extends Controller
{
    const ITERATION_SIZE = 50;

    public $limit = 100;

    public function init() {
//        $this->keySyncIsStartCurrency = Currency::syncProvider()->getStartKeyValue();
//        $this->keySyncIsStartProduct = Product::syncProvider()->getStartKeyValue();
//        $this->keySyncIsStartProductConfig = ProductCategoryConfig::syncProvider()->getStartKeyValue();
//        $this->keySyncIsStartProductCat = ProductCategory::syncProvider()->getStartKeyValue();
    }

    public function actionIndex()
    {
        $offset = 0;
        $result = array();
        do {
            //\Yii::error($offset, 'sync');
            $msCities = $this->ms_get_cities($offset);

            if (isset($msCities->rows) && !empty($msCities->rows)) {
                $result[$offset] = $msCities->rows;
                echo "Get data (offset={$offset}).\n";
            }

            $offset += $this->limit;
        } while (isset($msCities->rows) && !empty($msCities->rows));

        foreach ($result as $rows) {
            foreach ($rows as $item) {
                $code = isset($item->code) && $item->code ? (int)$item->code : 0;
                if ($code) {
                    $city = DeliveryCity::find()
                        ->where(['sdek_id' => $code])
                        ->one();
                    if ($city) {
                        $city->ms_id = $item->id;
                        $city->save();
                        echo "Row with id={$city->id} is updated.\n";
                    }
                }
            }
        }

        echo "Command is completed.\n";
    }

//    public function actionCreate($message = 'hello world is creating')
//    {
//        echo $message . "\n";
//    }

    private function ms_get_cities($offset = 0)
    {
        $msEntity = $this->ms_send_sync('entity/customentity/'.env('MS_ID_CUSTOM_ENTITY_DELIVERY_CITY'), $offset, null, $method = 'GET');

        $entity = array();
        if (isset($msEntity->responseContent)) {
            $entity = $msEntity->responseContent;
        }

        return $entity;
    }

    protected function ms_send_sync($url, $offset = 0, $data = null, $method = 'GET')
    {
        sleep(1);
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
            ->setUrl($url."?offset={$offset}&limit={$this->limit}")
            ->setHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => "Basic " . base64_encode(env('MS_LOGIN') . ':' . env('MS_PASSWORD')),
                'Accept-Encoding' => 'gzip, deflate, br',
            ])
            //->setData(['offset' => $offset, 'limit' => $this->limit])
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

        } catch (\Exception $e) {
            $result->status = false;
            $result->msg = (string)$e;
            return $result;
        }
    }
}
