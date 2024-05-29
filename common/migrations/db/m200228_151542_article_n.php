<?php

use yii\db\Migration;

/**
 * Class m200228_151542_article_n
 */
class m200228_151542_article_n extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%article_n}}', [
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

        $this->createIndex('idx_article_n_title', '{{%article_n}}', 'title', false);
        $this->createIndex('idx_article_n_slug', '{{%article_n}}', 'slug', false);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx_article_n_title', '{{%article_n}}');
        $this->dropIndex('idx_article_n_slug', '{{%article_n}}');
        $this->dropTable('{{%article_n}}');
    }
}
