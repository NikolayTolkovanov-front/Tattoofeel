<?php

namespace backend\modules\catalog\models\search;

use common\models\ProductSync;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class ProductSyncSearch extends ProductSync
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id','status','author'], 'string'],
            [['error', 'products'], 'safe'],
            [['date'], 'filter', 'filter' => 'strtotime', 'skipOnEmpty' => true],
            [['date'], 'default', 'value' => null],
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
        $query = ProductSync::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
            'author' => $this->author,
        ]);

        if ($this->date !== null) {
            $query->andFilterWhere(['between', 'date', $this->date, $this->date + 3600 * 24]);
        }

        $query->andFilterWhere(['like', 'products', $this->products])
            ->andFilterWhere(['like', 'error', $this->error]);

        return $dataProvider;
    }
}
