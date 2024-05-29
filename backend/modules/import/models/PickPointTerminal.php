<?php

namespace backend\modules\import\models;

use common\components\pickPoint\PickPoint;
use http\Exception;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "pick_point_terminal".
 *
 * @property integer $id
 * @property int $city_id
 * @property string $city_name
 * @property string $region
 *
 */
class PickPointTerminal extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%pick_point_terminal}}';
    }

    /**
     * @return PickPointTerminalQuery
     */
    public static function find()
    {
        return new PickPointTerminalQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'address',
                    'city_name',
                    'country_iso',
                    'country_name',
                    'file',
                    'house',
                    'in_description',
                    'out_description',
                    'name',
                    'number',
                    'owner_name',
                    'post_code',
                    'region',
                    'street',
                    'type_title',
                    'work_time',
                    'work_time_sms',
                ],
                'string'
            ],
            [['latitude', 'longitude'], 'number'],
            [
                ['card', 'cash', 'city_id', 'opening', 'owner_id', 'status', 'temporarily_closed', 'work_hourly'],
                'integer'
            ]
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
    public function importTerminals()
    {
        $pp = new PickPoint();
        $terminals = $pp->getPostamatList();

        if (!$terminals['status']){
            return [
                'status' => false,
                'msg' => 'No data received'
            ];
        }

        $sql = "TRUNCATE TABLE " . PickPointTerminal::tableName();
        try {
            Yii::$app->db->createCommand($sql)->execute();
        } catch (Exception $e) {
            return [
                'success' => 0,
                'error_message' => 'Error truncate table. ' . $e->getMessage()
            ];

        }

//        $url = Url::to('@app/web/uploads/pp_postamats.txt');
//        file_put_contents($url, json_encode($terminals));
//        $terminals = json_decode(file_get_contents($url), true);
//        Yii::warning($terminals, 'test');

        $fields = $this->getFields();
        $data = [];
        $counter = 0;

        foreach ($terminals['data'] as $terminal) {
            foreach ($fields as $db_field => $api_field) {
                $data[$counter][$db_field] = $terminal[$api_field];
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
            'address' => 'Address',
            'card' => 'Card',
            'cash' => 'Cash',
            'city_id' => 'CitiId',
            'city_name' => 'CitiName',
            'country_iso' => 'CountryIso',
            'country_name' => 'CountryName',
            'file' => 'FileI0',
            'house' => 'House',
            'terminal_id' => 'Id',
            'in_description' => 'InDescription',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'name' => 'Name',
            'number' => 'Number',
            'opening' => 'Opening',
            'out_description' => 'OutDescription',
            'owner_id' => 'OwnerId',
            'owner_name' => 'OwnerName',
            'post_code' => 'PostCode',
            'region' => 'Region',
            'status' => 'Status',
            'street' => 'Street',
            'work_hourly' => 'WorkHourly',
            'work_time' => 'WorkTime',
            'work_time_sms' => 'WorkTimeSMS',
            'type_title' => 'TypeTitle',
            'temporarily_closed' => 'TemporarilyClosed',
        ];
    }
}
