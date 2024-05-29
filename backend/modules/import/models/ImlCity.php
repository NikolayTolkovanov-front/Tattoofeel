<?php

namespace backend\modules\import\models;

use common\components\iml\Iml;
use http\Exception;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "iml_city".
 *
 * @property integer $id
 *
 */
class ImlCity extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%iml_city}}';
    }

    /**
     * @return ImlCityQuery
     */
    public static function find()
    {
        return new ImlCityQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'city',
                    'region',
                    'area',
                    'region_iml',
                    'rate_zone_moscow',
                    'rate_zone_spb',
                    'fias',
                ],
                'string'
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('common', 'ID'),
        ];
    }

    /**
     * @return array
     * @throws \yii\db\Exception
     */
    public function importCity()
    {
        $iml = new Iml();
        $cities = $iml->getRegionCityList();
//        $cities = file_get_contents(Url::to('@app/web/uploads/iml_city_region.json'));

        if (!$cities) {
            return [
                'success' => 0,
                'error_message' => 'No data received'
            ];
        }

        $sql = "TRUNCATE TABLE " . self::tableName();
        try {
            Yii::$app->db->createCommand($sql)->execute();
        } catch (Exception $e) {
            return [
                'success' => 0,
                'error_message' => 'Error truncate table. ' . $e->getMessage()
            ];

        }

        $fields = $this->getFields();
        $data = [];
        $counter = 0;

        foreach ($cities as $city) {
            foreach ($fields as $db_field => $api_field) {
                $data[$counter][$db_field] = $city[$api_field];
            }
            $counter++;
        }

        try {
            $result_rows = Yii::$app->db->createCommand()->batchInsert(self::tableName(), array_keys($fields),
                $data)->execute();
        } catch (\yii\db\Exception $e) {
            return [
                'success' => 0,
                'error_message' => 'Error added row. ' . $e->getMessage(),
            ];
        }

        return [
            'success' => 1,
            'data' => 'Inserted rows: ' . $result_rows,
        ];
    }

    private function getFields()
    {
        return [
            'city' => 'City',
            'region' => 'Region',
            'area' => 'Area',
            'region_iml' => 'RegionIML',
            'rate_zone_moscow' => 'RateZoneMoscow',
            'rate_zone_spb' => 'RateZoneSpb',
            'fias' => 'FIAS',
        ];
    }
}
