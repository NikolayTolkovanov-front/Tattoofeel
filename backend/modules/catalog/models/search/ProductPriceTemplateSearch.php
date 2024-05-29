<?php

namespace backend\modules\catalog\models\search;

use common\models\ProductPriceTemplate;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class ProductPriceTemplateSearch extends ProductPriceTemplate
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'string'],
            [['title'], 'safe'],
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
        $query = ProductPriceTemplate::find()
//            ->cache(7200)
        ;

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id
        ]);

        $query
            ->andFilterWhere(['like', 'title', $this->title]);

        return $dataProvider;
    }
}
