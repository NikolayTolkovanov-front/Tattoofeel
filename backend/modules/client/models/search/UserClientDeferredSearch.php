<?php

namespace backend\modules\client\models\search;

use common\models\UserClientDeferred;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * UserClientDeferredSearch represents the model behind the search form about `common\models\UserClientDeferred`.
 */
class UserClientDeferredSearch extends UserClientDeferred
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id','user_id','product_id'], 'integer'],
            [['created_at', 'updated_at'], 'filter', 'filter' => 'strtotime', 'skipOnEmpty' => true],
            [['created_at', 'updated_at'], 'default', 'value' => null],
            [['created_by', 'updated_by'], 'safe'],
            [['client_created_at'], 'filter', 'filter' => 'strtotime', 'skipOnEmpty' => true],
            [['client_created_at'], 'default', 'value' => null],
            [['client_created_by'], 'safe'],
        ];
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
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = UserClientDeferred::find();

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
            'client_created_by' => $this->client_created_by,
        ]);

        if ($this->created_at !== null) {
            $query->andFilterWhere(['between', 'created_at', $this->created_at, $this->created_at + 3600 * 24]);
        }

        if ($this->updated_at !== null) {
            $query->andFilterWhere(['between', 'updated_at', $this->updated_at, $this->updated_at + 3600 * 24]);
        }

        if ($this->client_created_at !== null) {
            $query->andFilterWhere(['between', 'created_at', $this->client_created_at, $this->client_created_at + 3600 * 24]);
        }

        $query->andFilterWhere(
            ['like','user_id',$this->user_id]
        );

        $query->andFilterWhere(
            ['like','product_id',$this->product_id]
        );

        return $dataProvider;
    }
}
