<?php

namespace backend\modules\catalog\models\search;

use common\models\SeoMetaTags;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class SeoMetaTagsSearch extends SeoMetaTags
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[
                'id',
                'url',
                'h1',
                'seo_title',
                'seo_desc',
                'seo_keywords',
                'seo_text',
            ], 'string'],
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
        $query = SeoMetaTags::find()->cache(7200);

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
        ]);

        if ($this->created_at !== null) {
            $query->andFilterWhere(['between', 'created_at', $this->created_at, $this->created_at + 3600 * 24]);
        }

        if ($this->updated_at !== null) {
            $query->andFilterWhere(['between', 'updated_at', $this->updated_at, $this->updated_at + 3600 * 24]);
        }

        $query->andFilterWhere(['like', 'url', $this->url]);
        $query->andFilterWhere(['like', 'h1', $this->h1]);
        $query->andFilterWhere(['like', 'seo_title', $this->seo_title]);
        $query->andFilterWhere(['like', 'seo_desc', $this->seo_desc]);
        $query->andFilterWhere(['like', 'seo_keywords', $this->seo_keywords]);
        $query->andFilterWhere(['like', 'seo_text', $this->seo_text]);

        return $dataProvider;
    }
}
