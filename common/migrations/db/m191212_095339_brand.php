<?php

use yii\db\Migration;

/**
 * Class m191212_095339_brand
 */
class m191212_095339_brand extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%brand}}', [
            'id' => $this->primaryKey(),
            'slug' => $this->string(128),
            'title' => $this->string(256)->notNull(),
            'status' => $this->tinyInteger()->notNull()->defaultValue(1),
            'isMain' => $this->tinyInteger()->notNull()->defaultValue(0),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'body' => $this->string(1024),
            'body_short' => $this->string(256),
            'thumbnail_path' => $this->string(128)
        ]);

        $this->createIndex('idx_brand_title', '{{%brand}}', 'title', false);
        $this->createIndex('idx_brand_slug', '{{%brand}}', 'slug', false);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx_brand_title', '{{%brand}}');
        $this->dropIndex('idx_brand_slug', '{{%brand}}');
        $this->dropTable('{{%brand}}');
    }
}
