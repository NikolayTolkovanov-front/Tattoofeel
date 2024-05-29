<?php

use yii\db\Migration;

/**
 * Class m200225_134428_slider_main
 */
class m200225_134428_slider_main extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%slider_main}}', [
            'id' => $this->primaryKey(),
            'url' => $this->string(128),
            'title' => $this->string(256)->notNull(),
            'status' => $this->tinyInteger()->notNull()->defaultValue(0),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'body_short' => $this->string(512),
            'thumbnail_path' => $this->string(128),
            'published_at' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%slider_main}}');
    }
}
