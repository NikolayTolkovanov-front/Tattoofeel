<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%admin_ip}}`.
 */
class m230307_144413_create_admin_ip_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%admin_ip}}', [
            'id' => $this->primaryKey(),
            'ip_address' => $this->string()->notNull(),
            'comment' => $this->text(),
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
        $this->dropTable('{{%admin_ip}}');
    }
}
