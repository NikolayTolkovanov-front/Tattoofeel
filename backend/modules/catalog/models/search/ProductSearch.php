<?php

namespace backend\modules\catalog\models\search;

use common\models\Product;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class ProductSearch extends Product
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[
                'id',
                'type_eq',
                'brand_id',
                'config_ms_id',
                'category_ms_id',
                'status',
                'is_ms_deleted',
                'article',
                'order',
                'created_by',
                'updated_by',
                'revised',
                'is_main_in_config',
                'ms_id',
                'manufacturer',
                'weight',
                'amount',
                'min_amount',
            ], 'string'],
            [['published_at', 'updated_at', 'created_at'], 'filter', 'filter' => 'strtotime', 'skipOnEmpty' => true],
            [['published_at', 'updated_at', 'created_at'], 'default', 'value' => null],
            [['slug', 'title', 'body'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Product::find();

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
            'category_ms_id' => $this->category_ms_id,
            'brand_id' => $this->brand_id,
            'type_eq' => $this->type_eq,
            'config_ms_id' => $this->config_ms_id,
            'status' => $this->status,
            //'is_ms_deleted' => $this->is_ms_deleted,
            'revised' => $this->revised,
            'is_main_in_config' => $this->is_main_in_config
        ]);

        if ($this->published_at !== null) {
            $query->andFilterWhere(['between', 'published_at', $this->published_at, $this->published_at + 3600 * 24]);
        }

        if ($this->created_at !== null) {
            $query->andFilterWhere(['between', 'created_at', $this->created_at, $this->created_at + 3600 * 24]);
        }

        if ($this->updated_at !== null) {
            $query->andFilterWhere(['between', 'updated_at', $this->updated_at, $this->updated_at + 3600 * 24]);
        }

        $query
            ->andFilterWhere(['like', 'article', $this->article])
            ->andFilterWhere(['like', 'order', $this->order])
            ->andFilterWhere(['like', 'ms_id', $this->ms_id])
            ->andFilterWhere(['like', 'manufacturer', $this->manufacturer])
            ->andFilterWhere(['like', 'weight', $this->weight])
            ->andFilterWhere(['like', 'amount', $this->amount])
            ->andFilterWhere(['like', 'min_amount', $this->min_amount])

            ->andFilterWhere(['like', 'slug', $this->slug])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'body', $this->body]);

        return $dataProvider;
    }
}
