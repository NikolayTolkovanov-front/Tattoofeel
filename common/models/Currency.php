<?php

namespace common\models;

use backend\components\sync\Sync;
use common\components\BlameableBehavior;
use common\components\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\base\Exception;

/**
 * This is the model class for table "currency".
 *
 * @property integer         $id
 * @property integer         $status
 * @property integer         $disable_sync
 * @property string          $code_iso
 * @property string          $fullName
 * @property double          $value
 * @property string          $error
 * @property integer         $created_at
 * @property integer         $created_by
 * @property integer         $updated_by
 * @property integer         $updated_at
 */
class Currency extends ActiveRecord
{
    use \common\models\traits\BlameAble;

    const SCENARIO_SYNC = 'sync';

    const STATUS_PUBLISHED = 1;
    const STATUS_DRAFT     = 0;

    //руб 643
    const DEFAULT_FRONTEND_DISPLAY_PRICE_CUR_ISO = '643';
    const DEFAULT_CART_PRICE_CUR_ISO = '643';

    /**
     * @return array statuses list
     */
    public static function statuses()
    {
        return [
            self::STATUS_DRAFT => t_b('нет'),
            self::STATUS_PUBLISHED => t_b('да'),
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%currency}}';
    }

    /** @inheritdoc */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            'blameable' => BlameableBehavior::class
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['code_iso'], 'required', 'except' => self::SCENARIO_SYNC],
            [['code_iso'], 'unique'],
            [['code_iso'], 'string', 'max' => 10],
            [['error','fullName'], 'string'],
            [['value'], 'double'],
            [['status','disable_sync'], 'integer'],
            [['status'], 'default', 'value' => 1],
        ];
    }

    /**
     * return @throws
     * */
    static function convertPrice($price, $from, $to) {

        if ($from == $to) return $price;

        $curs = Currency::find()->select('value')->indexBy('code_iso')->asArray()->all();

        if (isset($curs[$from]))
            $from_cur = $curs[$from];
        else throw new Exception("Нет курса в спрвочнике валют: $from");

        if (isset($curs[$to]))
            $to_cur = $curs[$to];
        else throw new Exception("Нет курса в спрвочнике валют: $to");

        return $price*$from_cur/$to_cur;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => t_b('Ид.'),
            'created_by' => t_b('Создал'),
            'updated_by' => t_b('Обновил'),
            'created_at' => t_b('Создано'),
            'updated_at' => t_b('Обновлено'),
            'code_iso' => t_b('Код iso'),
            'fullName' => t_b('Наименование'),
            'error' => t_b('Ошибки синх.'),
            'status' => t_b('Публиковать'),
            'disable_sync' => t_b('Отк. синх.'),
            'value' => t_b('Курс'),
        ];
    }

    static function syncProvider() {
        return new Sync([
            'key_start' => 'backend.currency.sync.isStart',
            'key_success' => 'backend.currency.sync.success_last_update',
            'key_error' => 'backend.currency.sync.error_last_update',
            'url_code' => 'CURRENCY',
            'model' => self::class,
            'model_sync_attrs' => ['value' => 'rate', 'fullName' => 'fullName'],
            'model_error_attr' => 'error',
            'model_sync_prop' => 'code_iso',
            'model_ms_sync_prop' => 'isoCode',
            'model_ms_skip' => [],//[643],
            'onError' => function ($result) {
                if (isset($result->error))
                    \Yii::getLogger()->log($result->error, 1, 'SyncCurrency');
            }
        ]);
    }
    static function sync($uid) {
        self::syncProvider()->sync($uid);
    }

}
