<?php

use yii\db\Migration;

class m140703_123104_page extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%page}}', [
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
        ]);

        $this->createIndex('idx_page_title', '{{%page}}', 'title', false);
        $this->createIndex('idx_page_slug', '{{%page}}', 'slug', false);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx_page_title', '{{%page}}');
        $this->dropIndex('idx_page_slug', '{{%page}}');
        $this->dropTable('{{%page}}');
    }
}
