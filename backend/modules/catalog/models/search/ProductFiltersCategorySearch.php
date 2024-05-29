<?php

namespace backend\modules\catalog\models\search;

use common\models\ProductFiltersCategory;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class ProductFiltersCategorySearch extends ProductFiltersCategory
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id','title'], 'string'],
            [['slug','updated_by','created_by','status'], 'safe'],
            [['updated_at', 'created_at'], 'filter', 'filter' => 'strtotime', 'skipOnEmpty' => true],
            [['updated_at', 'created_at'], 'default', 'value' => null],
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
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = ProductFiltersCategory::find()
//            ->cache(7200)
        ;

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'updated_by' => $this->updated_by,
            'status' => $this->status,
            'created_by' => $this->created_by,
        ]);

        if ($this->updated_at !== null) {
            $query->andFilterWhere(['between', 'updated_at', $this->updated_at, $this->updated_at + 3600 * 24]);
        }

        $query
            ->andFilterWhere(['like', 'slug', $this->slug])
            ->andFilterWhere(['like', 'title', $this->title]);

        return $dataProvider;
    }
}
