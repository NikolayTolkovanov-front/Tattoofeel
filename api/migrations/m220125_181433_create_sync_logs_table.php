<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%sync_logs}}`.
 */
class m220125_181433_create_sync_logs_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%sync_logs}}', [
            'id' => $this->primaryKey(),
            'created_at' => $this->integer()->notNull(),
            'entity_type' => $this->string(100)->notNull(),
            'event_type' => $this->string(40)->notNull(),
            'ms_id' => $this->string(40),
            'entity_id' => $this->integer(),
            'is_success' => $this->tinyInteger()->notNull()->defaultValue(0),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%sync_logs}}');
    }
}
