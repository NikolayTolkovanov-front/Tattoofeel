<?php

namespace backend\modules\content\models\search;

use common\models\BlockWidget;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class BlockWidgetSearch extends BlockWidget
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id','title','status','widget_id'], 'string'],
            [['url','updated_by','created_by'], 'safe'],
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
        $query = BlockWidget::find();

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

        if ($this->updated_at !== null) {
            $query->andFilterWhere(['between', 'updated_at', $this->updated_at, $this->updated_at + 3600 * 24]);
        }

        $query
            ->andFilterWhere(['like', 'widget_id', $this->widget_id])
            ->andFilterWhere(['like', 'url', $this->slug])
            ->andFilterWhere(['like', 'title', $this->title]);

        return $dataProvider;
    }
}
