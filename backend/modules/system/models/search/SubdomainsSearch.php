<?php

namespace backend\modules\system\models\search;

use common\models\Subdomains;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class SubdomainsSearch extends Subdomains
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['subdomain', 'city', 'word_form', 'address', 'phone', 'work_time', 'work_hours_showroom'], 'string'],
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
        $query = Subdomains::find();

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

        $query->andFilterWhere(['like', 'subdomain', $this->subdomain]);
        $query->andFilterWhere(['like', 'city', $this->subdomain]);

        return $dataProvider;
    }
}
