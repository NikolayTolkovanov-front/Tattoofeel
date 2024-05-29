<?php

namespace backend\modules\catalog\models\search;

use common\models\Reviews;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class ReviewsSearch extends Reviews
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[
                'id',
                //'user_client_id',
                //'product_id',
                'is_published',
                'text',
            ], 'string'],
            [['rating'], 'double'],
            [['date', 'updated_at', 'created_at'], 'filter', 'filter' => 'strtotime', 'skipOnEmpty' => true],
            [['date', 'updated_at', 'created_at'], 'default', 'value' => null],
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
        $query = Reviews::find()->cache(7200);

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
            'user_client_id' => $this->user_client_id,
            'product_id' => $this->product_id,
            'is_published' => $this->is_published,
            'rating' => $this->rating,
        ]);

        if ($this->active_until !== null) {
            $query->andFilterWhere(['between', 'date', $this->date, $this->date + 3600 * 24]);
        }

        if ($this->created_at !== null) {
            $query->andFilterWhere(['between', 'created_at', $this->created_at, $this->created_at + 3600 * 24]);
        }

        if ($this->updated_at !== null) {
            $query->andFilterWhere(['between', 'updated_at', $this->updated_at, $this->updated_at + 3600 * 24]);
        }

        $query->andFilterWhere(['like', 'text', $this->text]);

        return $dataProvider;
    }
}
