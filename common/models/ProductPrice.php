<?php

namespace common\models;

use common\components\BlameableBehavior;
use common\components\TimestampBehavior;
use common\models\traits\BlameAble;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "product_price".
 *
 * @property integer         $id
 * @property integer         $template_id
 * @property float           $price
 * @property string          $currency_isoCode
 * @property integer             $created_by
 * @property integer             $updated_by
 * @property integer             $created_at
 * @property integer             $updated_at
 * @property integer             $product_id
 *
 * @property User            $author
 * @property User            $updater
 * @property Product[]       $product
 */
class ProductPrice extends ActiveRecord
{
    use BlameAble;

    public $formatPrice;

    public static function currency($code_iso = null) {
        $res = array_replace(
            ArrayHelper::map(Currency::find()->asArray()->cache(600)->all(), 'code_iso', 'fullName'),
            [
                //'643' => ['₽','коп'],
                '643' => ['<span class="rub">i</span>','коп'],
                'USD' => ['$','¢'],
                'EUR' => ['€','¢']
            ]
        );

        return is_null($code_iso) ? $res :
            (isset($res[$code_iso]) ? $res[$code_iso] : null);
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%product_price}}';
    }

    /** @inheritdoc */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            'blameable' => BlameableBehavior::class,
        ];
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['template_id','product_id'], 'required'],
            [['price'], 'double'],
            [['currency_isoCode'], 'string'],
            [['currency_isoCode'], 'string', 'max' => 8],
            [['template_id', 'product_id'], 'integer'],
            [['formatPrice'], 'double'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => t_b('Ид.'),
            'template_id' => t_b('Скидка'),
            'price' => t_b('Цена'),
            'formatPrice' => t_b('Цена'),
            'currency_isoCode' => t_b('Валюта'),
            'product_id' => t_b('Продукт'),
            'created_by' => t_b('Создал'),
            'updated_by' => t_b('Обновил'),
            'created_at' => t_b('Создано'),
            'updated_at' => t_b('Обновлено')
        ];
    }

    static public function cent($v) {
        $r = explode('.', (string) $v);

        if ( $r[1] ) $r = $r[1];
        if ( $r < 10 ) $r = '0' . $r;
        if ( $r == 0 ) $r = '00';

        return $r;
    }

    static public function formatPrice($v) {
        return number_format((float)$v/100, 2, '.', '');
    }

    static public function ceilPrice($v) {
        return ceil((float)$v/100);
    }

    public function getCeilPrice() {
        return self::ceilPrice($this->price);
    }

    public function afterFind()
    {
        $this->formatPrice = self::formatPrice($this->price);
    }

    public function beforeSave($insert)
    {
        $this->price = $this->formatPrice*100;
        return parent::beforeSave($insert);
    }

    public function getDPrice($to, $addSale, $count = 1) {
        return self::getParsePrice($this->price, $this->currency_isoCode, $to, $addSale, $count);
    }

    static function getParsePrice($_price, $isoCode, $to = null, $addSale = 0, $count = 1) {

        $sale = (int) $addSale;
        $cur = self::currency($isoCode);

        if (!is_null($to) && !is_null(self::currency($to))) {
            $cur = self::currency($to);
            $price = Currency::convertPrice($_price, $isoCode, $to);
        } else $price = $_price;

        $price *= (100 + $sale) / 100;
        $priceCount = $price * $count;

        $formatPriceCount = self::formatPrice($priceCount);
        $formatPrice = self::formatPrice($price);

        return (object) [
            'cur' => $cur[0],
            'cur_cent' => $cur[1],
            'value' => $formatPriceCount,
            'floor' => floor($formatPriceCount),
            'ceil' => ceil($formatPriceCount),
            'ceil_fr' => number_format( ceil($formatPriceCount), 0, '', ' '),
            'floor_fr' => number_format( floor($formatPriceCount), 0, '', ' '),
            'cent' => self::cent($formatPriceCount),
            'sale' => $sale,
            'one' => (object) [
                'value' => $formatPrice,
                'floor' => floor($formatPrice),
                'ceil' => ceil($formatPrice),
                'cent' => self::cent($formatPrice),
            ]
        ];
    }

    public function getCartPrice($count = 1) {
        return $this->getDPrice(Currency::DEFAULT_CART_PRICE_CUR_ISO, $count);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::class, ['id' => 'product_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPriceTemplate()
    {
        return $this->hasOne(ProductPriceTemplate::class, ['id' => 'template_id']);
    }

    /**
     * @param array $productIds
     * @param string $template
     * @return array
     */
    public static function getByProductIdsAndTemplate(array $productIds, string $template): array
    {
        $result = [];
        $prices = self::find()
            ->where([ProductPrice::tableName() . '.product_id' => $productIds])
            ->andWhere([ProductPrice::tableName() . '.template_id' => $template])
            ->asArray()
            ->all();
        foreach ($prices as $price) {
            $result[$price->product_id] = $price;
        }

        return $result;
    }
}
