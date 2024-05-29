<?php

namespace backend\modules\client\models\search;

use common\models\UserClient;
use common\models\UserClientOrder;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * UserClientSearch represents the model behind the search form about `common\models\UserClient`.
 */
class UserClientSearch extends UserClient
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status'], 'integer'],
            [['username', 'auth_key', 'password_hash', 'email'], 'safe'],
            [['created_at', 'updated_at', 'logged_at'], 'filter', 'filter' => 'strtotime', 'skipOnEmpty' => true],
            [['created_at', 'updated_at', 'logged_at'], 'default', 'value' => null],
            [['created_by', 'updated_by'], 'safe'],
            [['client_created_at', 'client_updated_at'], 'filter', 'filter' => 'strtotime', 'skipOnEmpty' => true],
            [['client_created_at', 'client_updated_at'], 'default', 'value' => null],
            [['client_created_by', 'client_updated_by'], 'safe'],
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
        $query = UserClient::find();

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
            'client_updated_by' => $this->client_updated_by,
            'client_created_by' => $this->client_created_by,
        ]);

        if ($this->created_at !== null) {
            $query->andFilterWhere(['between', 'created_at', $this->created_at, $this->created_at + 3600 * 24]);
        }

        if ($this->updated_at !== null) {
            $query->andFilterWhere(['between', 'updated_at', $this->updated_at, $this->updated_at + 3600 * 24]);
        }

        if ($this->logged_at !== null) {
            $query->andFilterWhere(['between', 'logged_at', $this->logged_at, $this->logged_at + 3600 * 24]);
        }

        if ($this->client_created_at !== null) {
            $query->andFilterWhere(['between', 'client_created_at', $this->client_created_at, $this->client_created_at + 3600 * 24]);
        }

        if ($this->updated_at !== null) {
            $query->andFilterWhere(['between', 'client_updated_at', $this->client_updated_at, $this->client_updated_at + 3600 * 24]);
        }

        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'auth_key', $this->auth_key])
            ->andFilterWhere(['like', 'password_hash', $this->password_hash])
            ->andFilterWhere(['like', 'email', $this->email]);

        return $dataProvider;
    }
}
