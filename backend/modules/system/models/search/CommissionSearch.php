<?php

namespace backend\modules\system\models\search;

use common\models\Commission;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class CommissionSearch extends Commission
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
//            [['id', 'payment_type_id', 'discount_group'], 'string'],
            [['id'], 'string'],
            [['updated_by','created_by'], 'safe'],
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
        $query = Commission::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'updated_by' => $this->updated_by,
            'created_by' => $this->created_by,
        ]);

        if ($this->updated_at !== null) {
            $query->andFilterWhere(['between', 'updated_at', $this->updated_at, $this->updated_at + 3600 * 24]);
        }

//        $query->andFilterWhere(['like', 'payment_type_id', $this->payment_type_id]);
//        $query->andFilterWhere(['like', 'discount_group', $this->discount_group]);

        return $dataProvider;
    }
}
