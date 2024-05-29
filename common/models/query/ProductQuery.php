<?php

namespace common\models\query;

use common\models\Brand;
use common\models\UserClientOrder_Product;
use Yii;
use common\models\Currency;
use common\models\Product;
use common\models\ProductPrice;
use common\models\ProductPriceTemplate;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class ProductQuery extends ActiveQuery
{

    const SALE_EXPRESSION = 'if ( p2.price and p1.price > p2.price, floor((p1.price - p2.price)/p1.price*100), null )';

    /**
     * @return $this
     */
    public function preparePrice()
    {
        $salesBrandsSales = null;
        $template_id = ProductPriceTemplate::TEMPLATE_ID_DEFAULT;

        //цена для пользователя
        if (isset(Yii::$app->client) && !Yii::$app->user->isGuest) {
            $template_id = Yii::$app->client->identity->userProfile->saleTemplateId;

            $salesBrandsSales = Yii::$app->client->identity->userProfile->getSalesBrandsSales();
            $salesBrandsBrands = Yii::$app->client->identity->userProfile->getSalesBrandsBrands();
            $salesBrandsTmpIds = Yii::$app->client->identity->userProfile->getSalesBrandsTmpIds();
        };

        if (!isset($salesBrandsSales) || empty($salesBrandsSales)) {
            $salesQuery =
                'p1.template_id = "' . $template_id . '" and  p1.product_id = '.Product::tableName().'.id';
        } else {
            $idElt = 'ELT(FIELD('.Brand::tableName().'.slug, \''. implode('\',\'', $salesBrandsBrands) .'\'), 
                    \''. implode('\',\'', $salesBrandsTmpIds ) .'\')';

            $salesQuery =
                "p1.template_id = if ($idElt, $idElt, $template_id)" .
                ' and p1.product_id = '.Product::tableName().'.id';
        }

        $this->addSelect(new Expression(
            Product::tableName().
                '.*, if(p2.price, p1.price, 0) as clientOldPriceValue, if(p2.price, p2.price, p1.price) as clientPriceValue, '.
                self::SALE_EXPRESSION .' as clientSalePercent'
            ))
            ->leftJoin(Brand::tableName(), Brand::tableName().'.slug = '. Product::tableName().'.brand_id')
            ->leftJoin('('.
                (new Query())
                    ->select(new Expression('pp.template_id, pp.product_id, if ( pc.value, pp.price*pc.value, 0 ) as price'))
                    ->from(ProductPrice::tableName().'as pp')
                    ->leftJoin(Currency::tableName().'as pc')->createCommand()->rawSql.
                    'on pc.code_iso = pp.currency_isoCode) as p1',
                $salesQuery
            )
            ->leftJoin('('.
                (new Query())
                    ->select(new Expression('pp.template_id, pp.product_id, if ( pc.value, pp.price*pc.value, 0 ) as price'))
                    ->from(ProductPrice::tableName().'as pp')
                    ->leftJoin(Currency::tableName().'as pc')->createCommand()->rawSql.
                'on pc.code_iso = pp.currency_isoCode) as p2',

                'p2.template_id ='. ProductPriceTemplate::TEMPLATE_ID_SALE . ' and ' .
                'p2.product_id = '.Product::tableName().'.id'
            );

        return $this;
    }

    /**
     * @return $this
     */
    public function prepareConfig($hasFilters = false)
    {
        if (!$hasFilters) {
            $this->andWhere(
                ['or',
                    ['and',
                        [Product::tableName().'.is_main_in_config' => Product::IS_MAIN_IN_CONFIG__TRUE],
                        ['or',
                            ['<>', Product::tableName().'.config_ms_id', ''],
                            ['is not', Product::tableName().'.config_ms_id', null],
                        ]
                    ],
                    ['or',
                        [Product::tableName().'.config_ms_id' => ''],
                        [Product::tableName().'.config_ms_id' => null]
                    ]
                ]
            );
        }
        return $this;
    }

    /**
     * @return $this
     */
    public function published()
    {
        $this->andWhere(Product::queryPublished());
        return $this;
    }

    /**
     * @return $this
     */
    public function order()
    {
        $this->addOrderBy(Product::queryOrder());

        return $this;
    }

    private function getOtherLayoutRu($str){
        $array = array( "q"=>"й", "w"=>"ц", "e"=>"у", "r"=>"к",
            "t"=>"е", "y"=>"н", "u"=>"г", "i"=>"ш", "o"=>"щ",
            "p"=>"з", "["=>"х", "]"=>"ъ", "a"=>"ф", "s"=>"ы",
            "d"=>"в", "f"=>"а", "g"=>"п", "h"=>"р", "j"=>"о",
            "k"=>"л", "l"=>"д", ";"=>"ж", "'"=>"э", "z"=>"я",
            "x"=>"ч", "c"=>"с", "v"=>"м", "b"=>"и", "n"=>"т",
            "m"=>"ь", ","=>"б", "."=>"ю");
        $right_letter = strtr($str,$array);

        return $right_letter;
    }

    private function getOtherLayoutEn($str){
        $array = array('а' => 'f',	'б' => ',',	'в' => 'd',	'г' => 'u',	'д' => 'l',	'е' => 't',	'ё' => '`',
            'ж' => ';',	'з' => 'p',	'и' => 'b',	'й' => 'q',	'к' => 'r',	'л' => 'k',	'м' => 'v',
            'н' => 'y',	'о' => 'j',	'п' => 'g',	'р' => 'h',	'с' => 'c',	'т' => 'n',	'у' => 'e',
            'ф' => 'a',	'х' => '[',	'ц' => 'w',	'ч' => 'x',	'ш' => 'i',	'щ' => 'o',	'ь' => 'm',
            'ы' => 's',	'ъ' => ']',	'э' => "'",	'ю' => '.',	'я' => 'z',);
        $right_letter = strtr($str,$array);

        return $right_letter;
    }

    private function getTranslitEn($str)
    {
        $array = array(
            'а' => 'a',    'б' => 'b',    'в' => 'v',    'г' => 'g',    'д' => 'd',
            'е' => 'e',    'ё' => 'e',    'ж' => 'zh',   'з' => 'z',    'и' => 'i',
            'й' => 'y',    'к' => 'k',    'л' => 'l',    'м' => 'm',    'н' => 'n',
            'о' => 'o',    'п' => 'p',    'р' => 'r',    'с' => 's',    'т' => 't',
            'у' => 'u',    'ф' => 'f',    'х' => 'h',    'ц' => 'c',    'ч' => 'ch',
            'ш' => 'sh',   'щ' => 'sch',  'ь' => '',     'ы' => 'y',    'ъ' => '',
            'э' => 'e',    'ю' => 'yu',   'я' => 'ya',

            'А' => 'A',    'Б' => 'B',    'В' => 'V',    'Г' => 'G',    'Д' => 'D',
            'Е' => 'E',    'Ё' => 'E',    'Ж' => 'Zh',   'З' => 'Z',    'И' => 'I',
            'Й' => 'Y',    'К' => 'K',    'Л' => 'L',    'М' => 'M',    'Н' => 'N',
            'О' => 'O',    'П' => 'P',    'Р' => 'R',    'С' => 'S',    'Т' => 'T',
            'У' => 'U',    'Ф' => 'F',    'Х' => 'H',    'Ц' => 'C',    'Ч' => 'Ch',
            'Ш' => 'Sh',   'Щ' => 'Sch',  'Ь' => '',     'Ы' => 'Y',    'Ъ' => '',
            'Э' => 'E',    'Ю' => 'Yu',   'Я' => 'Ya',
        );

        $right_letter = strtr($str, $array);

        return $right_letter;
    }

    /**
     * @var $term
     * @return $this
     */
    public function search($term)
    {
//        echo "<pre>";print_r($term);echo "</pre>";
//        die('test');

        if ($term === '')
            return $this;

        $term = trim($term);
//        $term_even = preg_replace('/(.)./u', '$1_', $term);
//        $term_odd = preg_replace('/.(.)/u', '_$1', $term);

        $other_layout_term_ru = $this->getOtherLayoutRu($term);
//        $other_layout_term_even_ru = preg_replace('/(.)./u', '$1_', $other_layout_term_ru);
//        $other_layout_term_odd_ru = preg_replace('/.(.)/u', '_$1', $other_layout_term_ru);

        $other_layout_term_en = $this->getOtherLayoutEn($term);
//        $other_layout_term_even_en = preg_replace('/(.)./u', '$1_', $other_layout_term_en);
//        $other_layout_term_odd_en = preg_replace('/.(.)/u', '_$1', $other_layout_term_en);

        $translit_term_en = $this->getTranslitEn($term);


        $this->published()
            ->andFilterWhere(['or',
                ['like', Product::tableName().'.article', $term],
                ['like', Product::tableName().'.article', $other_layout_term_ru],
                ['like', Product::tableName().'.article', $other_layout_term_en],
                ['like', Product::tableName().'.article', $translit_term_en],
                ['like', Product::tableName().'.title', $term],
                ['like', Product::tableName().'.alt_desc', $term],
                ['like', Product::tableName().'.title', $other_layout_term_ru],
                ['like', Product::tableName().'.alt_desc', $other_layout_term_ru],
                ['like', Product::tableName().'.title', $other_layout_term_en],
                ['like', Product::tableName().'.alt_desc', $other_layout_term_en],
                ['like', Product::tableName().'.title', $translit_term_en],
                ['like', Product::tableName().'.alt_desc', $translit_term_en],
//                ['like', Product::tableName().'.title', "%$term_even%", false],
//                ['like', Product::tableName().'.title', "%$term_odd%", false],
//                ['like', Product::tableName().'.title', "%$other_layout_term_even_ru%", false],
//                ['like', Product::tableName().'.title', "%$other_layout_term_odd_ru%", false],
//                ['like', Product::tableName().'.title', "%$other_layout_term_even_en%", false],
//                ['like', Product::tableName().'.title', "%$other_layout_term_odd_en%", false],
            ]);

        if ($term != '') {
            $this->addOrderBy(new Expression(
                Product::tableName() . ".article like '%$term%' DESC," .
                Product::tableName() . ".title like '%$term%' DESC,".
                Product::tableName() . ".alt_desc like '%$term%' DESC"
            ));
        }

        $this->order();

        return $this;
    }

    /**
     * @return $this
     */
    public function popular()
    {
        $this->published()
            ->preparePrice()
            ->orderBy([Product::tableName().'.view_count' => SORT_DESC]);

        return $this;
    }

    /**
     * @return $this
     */
    public function new()
    {
        $this->andWhere([Product::tableName().'.is_new' => 1])
            ->andWhere(
                ['or',
                    ['>', Product::tableName().'.is_new_at', time()],
                    [Product::tableName().'.is_new_at' => null]
                ]
            )
            ->preparePrice()
            ->published()
            ->order();

        return $this;
    }

    /**
     * @return $this
     */
//    public function sale()
//    {
//        $this
//            ->published()
//            ->preparePrice()
//            ->andWhere(new Expression(self::SALE_EXPRESSION))
//            ->orderBy(new Expression(self::SALE_EXPRESSION.' DESC'));
//
//        return $this;
//    }

    /**
     * @return $this
     */
    public function sale()
    {
        $arDiscount = Product::find() // массив id товаров со скидкой
            ->select(Product::tableName() . '.config_ms_id')
            ->andWhere([Product::tableName() . '.is_discount' => 1])
            ->published()
            ->all();

        $discount = array();
        foreach ($arDiscount as $config) {
            $discount[] = $config->config_ms_id;
        }
        $discount = array_unique($discount);

        $this
            //->distinct()
            ->prepareConfig()
            ->andWhere([
                'in',
                Product::tableName() . '.config_ms_id',
                $discount
            ])
            ->published()
            ->preparePrice()
            ->order();

        return $this;
    }

    /**
     * @var $id
     * @return $this
     */
    public function category($ms_id)
    {
        $this->andFilterWhere([Product::tableName().'.category_ms_id' => $ms_id])
            ->preparePrice()
            ->published()
            ->order();

        return $this;
    }

    /**
     * @var array $arArticle
     * @return $this
     */
    public function similar($arArticle)
    {
//        $this->andFilterWhere(['in', Product::tableName().'.article', $arArticle])
//            ->preparePrice()
//            ->published()
//            ->order();

        if (!empty($arArticle)) {
            $arConfig = $arId = array();
            $arProducts = $this->where(['in', Product::tableName() . '.article', $arArticle])->all();
            if ($arProducts) {
                foreach ($arProducts as $item) {
                    $arConfig[] = $item->config_ms_id;
                }
            }

            if (!empty($arConfig)) {
                $arConfig = array_unique($arConfig);
                $arProducts = $this->where(['in', Product::tableName() . '.config_ms_id', $arConfig])->andWhere(['is_main_in_config' => 1])->all();
                if ($arProducts) {
                    foreach ($arProducts as $item) {
                        $arIds[] = $item->id;
                    }
                }
            }

            if (!empty($arIds)) {
                $arIds = array_unique($arIds);
                $this->andFilterWhere(['in', Product::tableName() . '.id', $arIds])
                    ->preparePrice()
                    ->published()
                    ->order();
            }
        }

        return $this;
    }

    /**
     * @var $id
     * @return $this
     */
    public function buy($id = null)
    {

        $oIds = ArrayHelper::getColumn(UserClientOrder_Product::find()
            ->select('order_id')
            ->andFilterWhere(['product_id' => $id])
            ->asArray()->all(),'order_id');

        $ids = ArrayHelper::getColumn(UserClientOrder_Product::find()
            ->select('product_id, count(*) as cnt')
            ->andFilterWhere(['in', 'order_id', $oIds])
            ->andWhere(['<>', 'product_id', $id])
            ->groupBy('product_id')
            ->orderBy('cnt DESC')
            ->asArray()->all(),'product_id');

        $this
            ->preparePrice()
            ->andWhere(['<>', Product::tableName().'.id', $id])
            ->published();

        $rOne = Product::findOne($id);
        if ($rOne && !empty($rOne->productRelated)) {
            $related = ArrayHelper::getColumn($rOne->productRelated,'id');
            if (!empty($related))
                $this->addOrderBy(new Expression(Product::tableName().'.id in (' . implode(',', $related) . ') DESC') );
        }

        if (!empty($ids))
            $this->addOrderBy(new Expression(Product::tableName().'.id in (' . implode(',', $ids) . ') DESC') );

        return $this->order();
    }

}
