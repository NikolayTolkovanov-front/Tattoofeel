<?php

namespace console\controllers;

use common\models\Product;
use DateTime;
use yii\base\InvalidConfigException;
use yii\console\Controller;
use yii\httpclient\Client;
use yii\httpclient\Exception;


class ProductSyncController extends Controller
{
    const BASE_SYNC_URL = 'entity/product/';
    const STOCK_SYNC_URL = 'report/stock/all/current/';
    public $limit = 1000;

    /**
     * @param string $url
     * @param null $data
     * @param string $method
     * @param string $api_version
     * @return object
     * @throws Exception
     * @throws InvalidConfigException
     */
    private function ms_send_sync(string $url, $data = null, string $method = 'GET', string $api_version = '1.2')
    {
        $result = (object)[
            'status' => true,
            'response' => null,
            'responseContent' => null,
            'json_error' => false,
            'msg' => null
        ];

        $client = new Client([
            'baseUrl' => 'https://api.moysklad.ru/api/remap/' . $api_version . '/',
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

        } catch (\Exception $e) {
            $result->status = false;
            $result->msg = (string)$e;
            return $result;
        }
    }

    public function actionAll() {
        $url = sprintf('%s%s', self::BASE_SYNC_URL, '?');

        $this->sync($url);
    }

    public function actionOne(string $productMsId) {
        $url = sprintf('%s%s', self::BASE_SYNC_URL, $productMsId);

        $response = $this->ms_send_sync($url, null, $method = 'GET', '1.2');

        if ($response->status) {
            $this->findAndSync($response->responseContent->id);
        }
    }

    /**
     * @throws \Exception
     */
    public function actionIndex(int $minutesFrom)
    {
        $updatedFrom = new DateTime(sprintf("-%d minutes", $minutesFrom));

        $filter = sprintf('?filter=%s', urlencode('updated>' . $updatedFrom->format('Y-m-d H:i:s')));
        $url = sprintf('%s%s', self::BASE_SYNC_URL, $filter);
        $this->sync($url);
        $updatedFrom = new DateTime(sprintf("-%d minutes", $minutesFrom));

        $url = self::STOCK_SYNC_URL.'?changedSince='.urlencode($updatedFrom->format('Y-m-d H:i:s'));
        $this->syncByStock($url);
    }

    private function sync($url)
    {
        $offset = 0;
        $totalProd = $successProd = $errorProd = 0;
        do {
            $url .= '&limit='.$this->limit;
            if ($offset) {
                $url .= '&offset='.$offset;
            }
            $response = $this->ms_send_sync($url, null, $method = 'GET', '1.2');

            if ($response->status) {
                if (isset($response->responseContent->rows) && is_array($response->responseContent->rows)) {
                    foreach ($response->responseContent->rows as $row) {
                        if (isset($row->id) && !empty($row->id)) {
                            $result = $this->findAndSync($row->id);
                            if ($result) {
                                $successProd++;
                            } else {
                                $errorProd++;
                            }
                        }

                        $totalProd++;
                        usleep(50000);
                    }
                }
            }

            $nextHref = $response->responseContent->meta->nextHref ?? '';
            $offset += $this->limit;
        } while (!empty($nextHref));

        echo self::getDateTimeForLog()."$totalProd products were processed (successful: $successProd, errors: $errorProd).\n";
        echo self::getDateTimeForLog()."Command is completed.\n";
    }

    private function syncByStock($url)
    {
        echo self::getDateTimeForLog()."Start sync by stock $url".PHP_EOL;
        $response = $this->ms_send_sync($url, null, $method = 'GET', '1.2');

        if (count($response->responseContent)) {
            echo self::getDateTimeForLog()."Sync by stock for ".count($response->responseContent)." products".PHP_EOL;
            foreach ($response->responseContent as $row) {
                if (isset($row->assortmentId) && !empty($row->assortmentId)) {
                    try {
                        echo self::getDateTimeForLog()."[DEBUG] Start sync by stock for assortment {$row->assortmentId}".PHP_EOL;

                        $product = Product::find()
                            ->where(['ms_id' => $row->assortmentId])
                            ->one();
                        if ($product) {
                            echo self::getDateTimeForLog()."[DEBUG] Found product {$product->id}".PHP_EOL;
                            $this->findAndSync($product->ms_id);
                        } else {
                            $responseProduct = $this->ms_send_sync("entity/product/{$row->assortmentId}");
                            if ($responseProduct->responseContent) {
                                echo self::getDateTimeForLog()."[DEBUG] Found product {$responseProduct->responseContent->id} by assortment {$row->assortmentId}".PHP_EOL;
                                $this->findAndSync($responseProduct->responseContent->id);
                            }
                        }
                    } catch (\Exception $e) {
                        echo self::getDateTimeForLog().$e->getMessage().PHP_EOL;
                        echo self::getDateTimeForLog().$e->getTraceAsString().PHP_EOL.PHP_EOL;
                    }
                }
                usleep(50000);
            }
        }
        echo self::getDateTimeForLog()."Finish sync by stock".PHP_EOL;
    }

    private static function getDateTimeForLog() {
        return date('[Y-m-d H:i:s]');
    }

    private function findAndSync(string $productMsId) {
        $product = Product::find()
            ->where(['ms_id' => $productMsId])
            ->one();

        if ($product) {
            $result = $product->syncUpdateProductHookHandler();

            if ($result) {
                echo self::getDateTimeForLog()."Product ID={$product->id} MS_ID={$productMsId} has been updated.\n";
            } else {
                echo self::getDateTimeForLog()."Product MS_ID={$productMsId} has not been updated.\n";
            }
        } else {
            $product = new Product(['ms_id' => $productMsId]);

            $result = $product->syncCreateProductHookHandler();

            if ($result) {
                echo self::getDateTimeForLog()."Product ID={$product->id} MS_ID={$productMsId} has been created.\n";
            } else {
                echo self::getDateTimeForLog()."Product MS_ID={$productMsId} has not been created.\n";
            }
        }

        return $result;
    }
}
