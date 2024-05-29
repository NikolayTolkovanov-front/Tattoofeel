<?php

use yii\db\Migration;

/**
 * Class m200225_150610_block_widget
 */
class m200225_150610_block_widget extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%block_widget}}', [
            'id' => $this->primaryKey(),
            'widget_id' => $this->string(128)->notNull(),
            'url' => $this->string(128),
            'title' => $this->string(256)->notNull(),
            'status' => $this->tinyInteger()->notNull()->defaultValue(0),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'body' => $this->text(),
            'body_short' => $this->string(512),
            'custom_1' => $this->string(512),
            'custom_2' => $this->string(512),
            'custom_3' => $this->string(512),
            'custom_4' => $this->string(512),
            'thumbnail_path' => $this->string(128),
        ]);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%block_widget}}');
    }
}
