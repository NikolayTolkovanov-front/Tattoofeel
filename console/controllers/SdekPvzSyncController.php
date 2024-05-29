<?php

namespace console\controllers;

use common\models\SdekPvz;
use yii\console\Controller;


class SdekPvzSyncController extends Controller
{
    public function actionIndex()
    {
        $sdekPvz = $this->getSdekPvz();

        if ($sdekPvz['status']) {
            if (isset($sdekPvz['data']) && is_array($sdekPvz['data'])) {
                foreach ($sdekPvz['data'] as $key => $item) {
                    if (isset($item['@attributes']['CityCode'])) {
                        $pvz_code = $item['@attributes']['Code'];
                        $sdek_id = (int)$item['@attributes']['CityCode'];
                        $record = SdekPvz::find()->where(['pvz_code' => $pvz_code])->one();

                        if ($record) {
                            $record->sdek_id = $sdek_id;
                            $record->xml = serialize($item);
                            $record->save();
                            echo "Row with id={$record->id} is updated.\n";
                        } else {
                            $record = new SdekPvz();
                            $record->pvz_code = $pvz_code;
                            $record->sdek_id = $sdek_id;
                            $record->xml = serialize($item);
                            $record->save();
                            echo "Row with id={$record->id} is created.\n";
                        }
                    }
//                    print_r($item);
//                    if ($key > 15) {
//                        break;
//                    }
                }
            }
        }

        echo "Command is completed.\n";
    }

    private function getSdekPvz()
    {
        $url = "http://integration.cdek.ru/pvzlist/v1/xml";

        try {
            $xml = file_get_contents($url);
        } catch (\Exception $e) {
            Yii::error($e->getMessage(), '_error');
            return [
                'status' => false,
                'msg' => $e->getMessage(),
            ];
        }
        $xml = simplexml_load_string($xml);
        $json = json_encode($xml);
        $result = json_decode($json, true);

        if (isset($result['Pvz']['@attributes'])) {
            //если вернулся только один ПВЗ
            $total_result[] = $result['Pvz'];
        } else {
            $total_result = $result['Pvz'];
        }

        if ($result) {
            return [
                'status' => true,
                'data' => $total_result,
            ];
        } else {
            return [
                'status' => false,
                'msg' => 'Данные не получены'
            ];
        }
    }
}
