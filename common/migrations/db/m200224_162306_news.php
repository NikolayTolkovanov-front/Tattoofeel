<?php

use yii\db\Migration;

/**
 * Class m200224_162306_news
 */
class m200224_162306_news extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%news}}', [
            'id' => $this->primaryKey(),
            'slug' => $this->string(128),
            'title' => $this->string(256)->notNull(),
            'status' => $this->tinyInteger()->notNull()->defaultValue(0),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'body' => $this->text(),
            'body_short' => $this->string(512),
            'thumbnail_path' => $this->string(128),
            'published_at' => $this->integer(),
        ]);

        $this->createIndex('idx_news_title', '{{%news}}', 'title', false);
        $this->createIndex('idx_news_slug', '{{%news}}', 'slug', false);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx_news_title', '{{%news}}');
        $this->dropIndex('idx_news_slug', '{{%news}}');
        $this->dropTable('{{%news}}');
    }
}
