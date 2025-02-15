<?php

namespace backend\modules\system\models\search;

use common\models\KeyStorageAppItem;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * KeyStorageItemSearch represents the model behind the search form about `common\models\KeyStorageAppItem`.
 */
class KeyStorageAppItemSearch extends KeyStorageAppItem
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['key', 'value'], 'string'],
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
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = KeyStorageAppItem::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'like', 'key', $this->key,
        ]);
        $query->andFilterWhere([
            'like', 'value', $this->value,
        ]);

        return $dataProvider;
    }
}
