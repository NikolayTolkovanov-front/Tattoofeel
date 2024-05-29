<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%seo_meta_tags}}`.
 */
class m230119_104325_create_seo_meta_tags_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%seo_meta_tags}}', [
            'id' => $this->primaryKey(),
            'url' => $this->string()->notNull(),
            'h1' => $this->string(),
            'seo_title' => $this->text(),
            'seo_desc' => $this->text(),
            'seo_keywords' => $this->text(),
            'seo_text' => $this->text(),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%seo_meta_tags}}');
    }
}