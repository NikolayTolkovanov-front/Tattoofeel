<?php

namespace common\moy_sklad;

use common\moy_sklad\entities\_Entity;
use frontend\helpers\Debug as _;

use yii\httpclient\Client as HttpClient;

class Client
{
    const LOG_PREFIX = 'MS Client';

    private $httpClient;

    public function __construct()
    {
        $this->httpClient = new HttpClient([
            'baseUrl' => \Yii::$app->params['moy_sklad']['url'],
            'requestConfig' => [
                'format' => HttpClient::FORMAT_JSON
            ],
        ]);
    }

    public function send(_Entity $entity, $method = 'POST')
    {
        _::step('ms', self::LOG_PREFIX, '==========');

        $result = [];

        $request = $this->httpClient->createRequest()
            ->setMethod($method)
            ->setUrl($entity->path())
            ->setHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => "Basic " . base64_encode(env('MS_LOGIN') . ':' . env('MS_PASSWORD')),
                'Accept-Encoding' => 'gzip, deflate, br',
            ])
            ->addOptions([
                'timeout' => 15
            ]);

        if ($data = $entity->buildFields()) {
            $request->setData($data);
        }

        _::step('ms', self::LOG_PREFIX, 'send');
        _::value('ms', self::LOG_PREFIX, 'url', $entity->url());

        $response = $request->send();
        $result['success'] = $response->isOk;

        _::value('ms', self::LOG_PREFIX, 'response isOk', $response->isOk);

        if (!$response->isOk) {
            $result['message'] = 'MS error';

            _::value('ms', self::LOG_PREFIX, 'request data', $data);
            _::value('ms', self::LOG_PREFIX, 'response', $response->content);
            _::step('ms', self::LOG_PREFIX, 'fail');
        } else {
            try {
                if (isset($response->content)) {
                    if ($response->headers['content-encoding'] == 'gzip') {
                        $responseContent = gzdecode($response->content);
                    } else $responseContent = $response->content;

                    $responseContent = json_decode($responseContent);

                    $e = json_last_error();
                    if (is_null($responseContent) || $e) {
                        $result['message'] = 'JSON parse error';

                        _::value('ms', self::LOG_PREFIX, 'request data', $data);
                        _::value('ms', self::LOG_PREFIX, 'response', $responseContent);
                        _::value('ms', self::LOG_PREFIX, 'error', $result['message']);
                        _::step('ms', self::LOG_PREFIX, 'fail');
                    } else {
                        $result['data'] = $responseContent;
                    }
                }

                _::step('ms', self::LOG_PREFIX, 'success');
            } catch (\Exception $e) {
                $result['message'] = 'Runtime error';

                _::value('ms', self::LOG_PREFIX, 'error', $e->getMessage());
                _::step('ms', self::LOG_PREFIX, 'fail');
            }
        }

        return $result;
    }
}