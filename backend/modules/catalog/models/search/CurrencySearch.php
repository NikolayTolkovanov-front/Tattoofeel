<?php

namespace backend\modules\catalog\models\search;

use common\models\Currency;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class CurrencySearch extends Currency
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id','code_iso','value','fullName','status'], 'string'],
            [['error','updated_by','created_by'], 'safe'],
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
        $query = Currency::find()
            //->cache(7200)
        ;

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
            'updated_by' => $this->updated_by,
            'created_by' => $this->created_by,
        ]);

        if ($this->created_at !== null) {
            $query->andFilterWhere(['between', 'created_at', $this->created_at, $this->created_at + 3600 * 24]);
        }

        if ($this->updated_at !== null) {
            $query->andFilterWhere(['between', 'updated_at', $this->updated_at, $this->updated_at + 3600 * 24]);
        }

        $query
            ->andFilterWhere(['like', 'code_iso', $this->code_iso])
            ->andFilterWhere(['like', 'fullName', $this->fullName])
            ->andFilterWhere(['like', 'value', $this->value])
            ->andFilterWhere(['like', 'error', $this->error]);

        return $dataProvider;
    }
}
