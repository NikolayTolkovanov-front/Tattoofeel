<?php

namespace api\modules\v1\resources;

use yii\helpers\Url;
use yii\web\Link;
use yii\web\Linkable;

/**
 * @author Eugene Terentev <eugene@terentev.net>
 */
class Article extends \common\models\ArticleN implements Linkable
{
    public function fields()
    {
        return [
            'id',
            'slug',
            'title',
            'status',
            'created_by',
            'updated_by',
            'created_at',
            'updated_at',
            'body',
            'body_short',
            'thumbnail_path',
            'published_at',
            'seo_title',
            'seo_desc',
            'seo_keywords',
            //'links',
        ];

    }

    /**
     * Returns a list of links.
     *
     * @return array the links
     */
    public function getLinks()
    {
        return [
            Link::REL_SELF => Url::to(['article/view', 'id' => $this->id], true)
        ];
    }
}
