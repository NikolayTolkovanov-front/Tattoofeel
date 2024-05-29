<?php

namespace backend\modules\catalog\models\search;

use common\models\Coupons;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class CouponsSearch extends Coupons
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[
                'id',
                'coupon_code',
                'uses_count',
                'used_count',
                'is_percent',
                'is_one_user',
                'is_one_product',
                'coupon_value',
                'order_sum_min',
            ], 'string'],
            [['active_until', 'updated_at', 'created_at'], 'filter', 'filter' => 'strtotime', 'skipOnEmpty' => true],
            [['active_until', 'updated_at', 'created_at'], 'default', 'value' => null],
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [];
    }

//    /**
//     * @inheritdoc
//     */
//    public function scenarios()
//    {
//        // bypass scenarios() implementation in the parent class
//        return Model::scenarios();
//    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Coupons::find()->cache(7200);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'uses_count' => $this->uses_count,
            'used_count' => $this->used_count,
            'is_percent' => $this->is_percent,
            'is_one_user' => $this->is_one_user,
            'is_one_product' => $this->is_one_product,
            'coupon_value' => $this->coupon_value,
            'order_sum_min' => $this->order_sum_min,
        ]);

        if ($this->active_until !== null) {
            $query->andFilterWhere(['between', 'active_until', $this->active_until, $this->active_until + 3600 * 24]);
        }

        if ($this->created_at !== null) {
            $query->andFilterWhere(['between', 'created_at', $this->created_at, $this->created_at + 3600 * 24]);
        }

        if ($this->updated_at !== null) {
            $query->andFilterWhere(['between', 'updated_at', $this->updated_at, $this->updated_at + 3600 * 24]);
        }

        $query->andFilterWhere(['like', 'coupon_code', $this->coupon_code]);

        return $dataProvider;
    }
}
